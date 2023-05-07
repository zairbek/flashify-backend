<?php

declare(strict_types=1);

namespace MarketPlace\Market\Auth\Application\Service;

use Exception;
use JsonException;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use MarketPlace\Common\Domain\Events\EventDispatcher;
use MarketPlace\Common\Domain\Exceptions\UserIsBannedException;
use MarketPlace\Common\Domain\Exceptions\UserIsInactiveException;
use MarketPlace\Common\Domain\ValueObject\ConfirmationCode;
use MarketPlace\Common\Domain\ValueObject\CreatedAt;
use MarketPlace\Common\Domain\ValueObject\Email;
use MarketPlace\Common\Domain\ValueObject\Phone;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Market\Auth\Application\Dto\RefreshingTokenDto;
use MarketPlace\Market\Auth\Application\Dto\SendCodeForSignInViaEmailDto;
use MarketPlace\Market\Auth\Application\Dto\SendCodeForSignInViaPhoneDto;
use MarketPlace\Market\Auth\Application\Dto\SignInWithEmailDto;
use MarketPlace\Market\Auth\Application\Dto\SignInWithPhoneDto;
use MarketPlace\Market\Auth\Application\Dto\SignOutDto;
use MarketPlace\Market\Auth\Domain\Adapter\UserAdapterInterface;
use MarketPlace\Market\Auth\Domain\Entity\PhoneNumber;
use MarketPlace\Market\Auth\Domain\Entity\User;
use MarketPlace\Market\Auth\Domain\Events\SendConfirmationCodeForEmailEvent;
use MarketPlace\Market\Auth\Domain\Events\SendConfirmationCodeForPhoneNumberEvent;
use MarketPlace\Market\Auth\Domain\Events\UserAuthorizedEvent;
use MarketPlace\Market\Auth\Domain\Repository\TokenRepositoryInterface;
use MarketPlace\Market\Auth\Domain\ValueObject\JwtTokenId;
use MarketPlace\Market\Auth\Domain\ValueObject\RefreshToken;
use MarketPlace\Market\Auth\Domain\ValueObject\Token;
use MarketPlace\Market\Auth\Infrastructure\Exception\ConfirmationCodeIsNotMatchException;
use MarketPlace\Market\Auth\Infrastructure\Exception\NotGivenClientIdAndSecretForTokenServiceException;
use MarketPlace\Market\Auth\Infrastructure\Exception\SendSmsThrottleException;
use MarketPlace\Market\Auth\Infrastructure\Exception\UserEmailNotFoundException;
use MarketPlace\Market\Auth\Infrastructure\Exception\UserNotFoundException;
use MarketPlace\Market\Auth\Infrastructure\Exception\UserPhoneNotFoundException;
use MarketPlace\Market\Auth\Infrastructure\Listener\SendConfirmationCodeForEmailListener;
use MarketPlace\Market\Auth\Infrastructure\Listener\SendConfirmationCodeToPhoneNumberListener;
use MarketPlace\Market\Auth\Infrastructure\Listener\UserAuthorizedListener;
use MarketPlace\Market\Auth\Infrastructure\Service\TokenService;

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
            SendConfirmationCodeForEmailListener::class
        ]
    ];

    private UserAdapterInterface $userAdapter;
    private EventDispatcher $eventDispatcher;
    private TokenRepositoryInterface $tokenRepository;

    public function __construct(UserAdapterInterface $userAdapter, TokenRepositoryInterface $tokenRepository)
    {
        $this->userAdapter = $userAdapter;
        $this->eventDispatcher = new EventDispatcher($this->listeners);
        $this->tokenRepository = $tokenRepository;
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
     * @param SignInWithPhoneDto $dto
     * @return Token
     * @throws ConfirmationCodeIsNotMatchException
     * @throws JsonException
     * @throws OAuthServerException
     * @throws UniqueTokenIdentifierConstraintViolationException
     * @throws UserIsBannedException
     * @throws UserIsInactiveException
     * @throws UserNotFoundException
     * @throws UserPhoneNotFoundException
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

        return (new TokenService())->generate($user);
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

    /**
     * @param SignInWithEmailDto $dto
     * @return Token
     * @throws ConfirmationCodeIsNotMatchException
     * @throws UserEmailNotFoundException
     * @throws UserIsBannedException
     * @throws UserIsInactiveException
     * @throws UserNotFoundException
     * @throws JsonException
     * @throws OAuthServerException
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function signInWithEmail(SignInWithEmailDto $dto): Token
    {
        $email = new Email($dto->email);
        $userEmail = $this->userAdapter->findUserEmail($email);

        if ($userEmail->isCodeNotMatch(new ConfirmationCode($dto->confirmationCode))) {
            throw new ConfirmationCodeIsNotMatchException();
        }

        $userEmail->clearTempData();
        $this->userAdapter->updateUserEmail($userEmail);

        $user = $this->userAdapter->findUser($userEmail->getUserUuid());
        if ($user->getStatus()->isBanned()) {
            throw new UserIsBannedException();
        }
        if ($user->getStatus()->isInactive()) {
            throw new UserIsInactiveException();
        }
        $user->authorize();

        return (new TokenService())->generate($user);
    }

    /**
     * @param RefreshingTokenDto $dto
     * @return Token
     * @throws JsonException
     * @throws OAuthServerException
     * @throws NotGivenClientIdAndSecretForTokenServiceException
     */
    public function refreshingToken(RefreshingTokenDto $dto): Token
    {
        return (new TokenService())->refreshing(new RefreshToken(refreshToken: $dto->refreshToken));
    }

    public function signOut(SignOutDto $dto): void
    {
        $jwtTokenId = JwtTokenId::fromJwtToken($dto->bearerToken);

        $this->tokenRepository->signOut($jwtTokenId);
    }
}
