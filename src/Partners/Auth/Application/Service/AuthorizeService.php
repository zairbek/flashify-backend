<?php

declare(strict_types=1);

namespace MarketPlace\Partners\Auth\Application\Service;

use Exception;
use JsonException;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use MarketPlace\Common\Domain\Events\EventDispatcher;
use MarketPlace\Common\Domain\Exceptions\UserIsBannedException;
use MarketPlace\Common\Domain\Exceptions\UserIsInactiveException;
use MarketPlace\Partners\Auth\Application\Dto\RefreshingTokenDto;
use MarketPlace\Market\Auth\Application\Dto\SignOutDto;
use MarketPlace\Partners\Auth\Application\Dto\SendCodeForSignInViaEmailDto;
use MarketPlace\Partners\Auth\Application\Dto\SendCodeForSignInViaPhoneDto;
use MarketPlace\Partners\Auth\Application\Dto\SignInWithEmailDto;
use MarketPlace\Partners\Auth\Application\Dto\SignInWithPhoneDto;
use MarketPlace\Market\Auth\Domain\Events\SendConfirmationCodeForEmailEvent;
use MarketPlace\Market\Auth\Domain\Events\SendConfirmationCodeForPhoneNumberEvent;
use MarketPlace\Market\Auth\Domain\Events\UserAuthorizedEvent;
use MarketPlace\Partners\Auth\Domain\ValueObject\Email;
use MarketPlace\Partners\Auth\Domain\ValueObject\JwtTokenId;
use MarketPlace\Partners\Auth\Domain\ValueObject\RefreshToken;
use MarketPlace\Partners\Auth\Domain\ValueObject\Token;
use MarketPlace\Partners\Auth\Infrastructure\Exception\ConfirmationCodeIsNotMatchException;
use MarketPlace\Partners\Auth\Infrastructure\Exception\NotGivenClientIdAndSecretForTokenServiceException;
use MarketPlace\Partners\Auth\Infrastructure\Exception\SendSmsThrottleException;
use MarketPlace\Partners\Auth\Infrastructure\Exception\UserNotFoundException;
use MarketPlace\Partners\Auth\Infrastructure\Exception\UserPhoneNotFoundException;
use MarketPlace\Partners\Auth\Infrastructure\Listener\SendConfirmationCodeForEmailListener;
use MarketPlace\Partners\Auth\Infrastructure\Listener\SendConfirmationCodeToPhoneNumberListener;
use MarketPlace\Partners\Auth\Infrastructure\Listener\UserAuthorizedListener;
use MarketPlace\Partners\Auth\Infrastructure\Service\TokenService;
use MarketPlace\Partners\Auth\Domain\Adapter\UserAdapterInterface;
use MarketPlace\Partners\Auth\Domain\Repository\TokenRepositoryInterface;
use MarketPlace\Partners\Auth\Domain\ValueObject\Phone;

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
     * @throws UserNotFoundException
     * @throws UserPhoneNotFoundException
     */
    public function sendCodeForSignInViaPhone(SendCodeForSignInViaPhoneDto $dto): void
    {
        $phone = Phone::fromString($dto->regionIsoCode, $dto->phone);

        $user = $this->userAdapter->findByPhone($phone);
        $user->sendSmsConfirmationCode();
        $this->userAdapter->update($user);

        $this->eventDispatcher->dispatch($user->releaseEvents());
    }

    /**
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
        $user = $this->userAdapter->findByPhone($phone);
        if (! $user->smsCodeCorrect($dto->confirmationCode)) {
            throw new ConfirmationCodeIsNotMatchException();
        }

        $user->clearTempData();
        $this->userAdapter->update($user);

        if ($user->getStatus()->isBanned()) {
            throw new UserIsBannedException();
        }
        if ($user->getStatus()->isInactive()) {
            throw new UserIsInactiveException();
        }

        $user->authorize();
        $this->eventDispatcher->dispatch($user->releaseEvents());

        return (new TokenService())->generate($user);
    }

    /**
     * @throws SendSmsThrottleException
     * @throws UserNotFoundException
     */
    public function sendCodeForSignInViaEmail(SendCodeForSignInViaEmailDto $dto): void
    {
        $email = new Email($dto->email);
        $user = $this->userAdapter->findByEmail($email);
        $user->sendEmailConfirmationCode();
        $this->userAdapter->update($user);
        $this->eventDispatcher->dispatch($user->releaseEvents());
    }

    /**
     * @throws ConfirmationCodeIsNotMatchException
     * @throws JsonException
     * @throws OAuthServerException
     * @throws UniqueTokenIdentifierConstraintViolationException
     * @throws UserIsBannedException
     * @throws UserIsInactiveException
     * @throws UserNotFoundException
     */
    public function signInWithEmail(SignInWithEmailDto $dto): Token
    {
        $email = new Email($dto->email);
        $user = $this->userAdapter->findByEmail($email);

        if (! $user->emailCodeCorrect($dto->confirmationCode)) {
            throw new ConfirmationCodeIsNotMatchException();
        }

        $user->clearTempData();
        $this->userAdapter->update($user);

        if ($user->getStatus()->isBanned()) {
            throw new UserIsBannedException();
        }
        if ($user->getStatus()->isInactive()) {
            throw new UserIsInactiveException();
        }

        $user->authorize();
        $this->eventDispatcher->dispatch($user->releaseEvents());

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
