<?php

declare(strict_types=1);

namespace MarketPlace\Market\Auth\Application\Service;

use Exception;
use MarketPlace\Common\Domain\Events\EventDispatcher;
use MarketPlace\Common\Domain\Exceptions\UserIsBannedException;
use MarketPlace\Common\Domain\Exceptions\UserIsInactiveException;
use MarketPlace\Common\Domain\ValueObject\ConfirmationCode;
use MarketPlace\Common\Domain\ValueObject\CreatedAt;
use MarketPlace\Common\Domain\ValueObject\Email;
use MarketPlace\Common\Domain\ValueObject\Phone;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Market\Auth\Application\Dto\SendCodeForSignInViaEmailDto;
use MarketPlace\Market\Auth\Application\Dto\SendCodeForSignInViaPhoneDto;
use MarketPlace\Market\Auth\Application\Dto\SignInWithPhoneDto;
use MarketPlace\Market\Auth\Domain\Adapter\UserAdapterInterface;
use MarketPlace\Market\Auth\Domain\Entity\PhoneNumber;
use MarketPlace\Market\Auth\Domain\Entity\User;
use MarketPlace\Market\Auth\Domain\Events\SendConfirmationCodeForEmailEvent;
use MarketPlace\Market\Auth\Domain\Events\SendConfirmationCodeForPhoneNumberEvent;
use MarketPlace\Market\Auth\Domain\Events\UserAuthorizedEvent;
use MarketPlace\Market\Auth\Domain\ValueObject\Token;
use MarketPlace\Market\Auth\Infrastructure\Exception\ConfirmationCodeIsNotMatchException;
use MarketPlace\Market\Auth\Infrastructure\Exception\SendSmsThrottleException;
use MarketPlace\Market\Auth\Infrastructure\Exception\UserEmailNotFoundException;
use MarketPlace\Market\Auth\Infrastructure\Exception\UserNotFoundException;
use MarketPlace\Market\Auth\Infrastructure\Exception\UserPhoneNotFoundException;
use MarketPlace\Market\Auth\Infrastructure\Listener\SendConfirmationCodeToPhoneNumberListener;
use MarketPlace\Market\Auth\Infrastructure\Listener\UserAuthorizedListener;

class AuthorizeService
{
    private array $listeners = [
        SendConfirmationCodeForPhoneNumberEvent::class => [
            SendConfirmationCodeToPhoneNumberListener::class
        ],
        UserAuthorizedEvent::class => [
            UserAuthorizedListener::class
        ],
        SendConfirmationCodeForEmailEvent::class => [

        ]
    ];

    private UserAdapterInterface $userAdapter;
    private EventDispatcher $eventDispatcher;

    public function __construct(UserAdapterInterface $userAdapter)
    {
        $this->userAdapter = $userAdapter;
        $this->eventDispatcher = new EventDispatcher($this->listeners);
    }

    /**
     * @throws SendSmsThrottleException
     * @throws Exception
     */
    public function sendCodeForSignInViaPhone(SendCodeForSignInViaPhoneDto $dto): void
    {
        $phone = Phone::fromString($dto->regionIsoCode, $dto->phone);

        try {
            $userPhone = $this->userAdapter->findUserPhone($phone);
            $userPhone->sendConfirmationCode();

            $this->userAdapter->updateUserPhone($userPhone);
        } catch (UserPhoneNotFoundException $e) {
            $code = ConfirmationCode::generate();
            $userPhone = new PhoneNumber(
                uuid: Uuid::next(),
                phone: $phone,
                createdAt: CreatedAt::now(),
                confirmationCode: $code,
            );
            $userPhone->sendConfirmationCode($code);

            $this->userAdapter->createUserPhone($userPhone);
        }

        $this->eventDispatcher->dispatch($userPhone->releaseEvents());
    }

    /**
     * @throws UserPhoneNotFoundException
     * @throws ConfirmationCodeIsNotMatchException
     * @throws UserNotFoundException
     * @throws UserIsBannedException
     * @throws UserIsInactiveException
     */
    public function signInWithPhone(SignInWithPhoneDto $dto): Token
    {
        $phone = Phone::fromString($dto->regionIsoCode, $dto->phone);
        $userPhone = $this->userAdapter->findUserPhone($phone);
        if ($userPhone->isCodeNotMatch(new ConfirmationCode($dto->confirmationCode))) {
            throw new ConfirmationCodeIsNotMatchException();
        }

        $userPhone->clearTempData();
        $this->userAdapter->updateUserPhone($userPhone);

        if ($userPhone->getUserId()) {
            $user = $this->userAdapter->findUser($userPhone->getUserId());
            if ($user->getStatus()->isBanned()) {
                throw new UserIsBannedException();
            }
            if ($user->getStatus()->isInactive()) {
                throw new UserIsInactiveException();
            }
        } else {
            $user = User::createViaPhone($userPhone);
            $this->userAdapter->createUserViaPhone($user);
        }

        $user->authorize();
        $this->eventDispatcher->dispatch($user->releaseEvents());

        return $this->userAdapter->authorize($user);
    }

    /**
     * @throws SendSmsThrottleException
     * @throws UserEmailNotFoundException
     */
    public function sendCodeForSignInViaEmail(SendCodeForSignInViaEmailDto $dto): void
    {
        $email = new Email($dto->email);

        $userEmail = $this->userAdapter->findUserEmail($email);
        $userEmail->sendConfirmationCode();

        $this->userAdapter->updateUserEmail($userEmail);
        $this->eventDispatcher->dispatch($userEmail->releaseEvents());
    }
}
