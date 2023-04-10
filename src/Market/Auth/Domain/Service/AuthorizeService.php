<?php

declare(strict_types=1);

namespace MarketPlace\Market\Auth\Domain\Service;

use Exception;
use MarketPlace\Common\Domain\Exceptions\UserIsBannedException;
use MarketPlace\Common\Domain\Exceptions\UserIsInactiveException;
use MarketPlace\Common\Domain\ValueObject\ConfirmationCode;
use MarketPlace\Common\Domain\ValueObject\CreatedAt;
use MarketPlace\Common\Domain\ValueObject\Phone;
use MarketPlace\Common\Domain\ValueObject\SendAt;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Market\Auth\Application\Dto\SendCodeForSignInViaPhoneDto;
use MarketPlace\Market\Auth\Application\Dto\SignInWithPhoneDto;
use MarketPlace\Market\Auth\Domain\Adapter\UserAdapterInterface;
use MarketPlace\Market\Auth\Domain\Entity\PhoneNumber;
use MarketPlace\Market\Auth\Domain\Entity\User;
use MarketPlace\Market\Auth\Domain\Exception\ConfirmationCodeIsNotMatchException;
use MarketPlace\Market\Auth\Domain\Exception\SendSmsThrottleException;
use MarketPlace\Market\Auth\Domain\Exception\UserNotFoundException;
use MarketPlace\Market\Auth\Domain\Exception\UserPhoneNotFoundException;
use MarketPlace\Market\Auth\Domain\ValueObject\Token;

class AuthorizeService
{
    private UserAdapterInterface $userAdapter;

    public function __construct(UserAdapterInterface $userAdapter)
    {
        $this->userAdapter = $userAdapter;
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
                sendAt: SendAt::now()
            );
            $userPhone->sendConfirmationCode($code);

            $this->userAdapter->createUserPhone($userPhone);
        }

        $userPhone->releaseEvents();
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
        }

        $user->authorize();
        $user->releaseEvents();

        return $this->userAdapter->authorize($user);
    }
}
