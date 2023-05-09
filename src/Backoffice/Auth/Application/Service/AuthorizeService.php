<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Auth\Application\Service;

use JsonException;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use MarketPlace\Backoffice\Auth\Application\Dto\RefreshingTokenDto;
use MarketPlace\Backoffice\Auth\Application\Dto\SignInDto;
use MarketPlace\Backoffice\Auth\Application\Dto\SignOutDto;
use MarketPlace\Backoffice\Auth\Domain\Adapter\UserAdapterInterface;
use MarketPlace\Backoffice\Auth\Domain\Events\UserAuthorizedEvent;
use MarketPlace\Backoffice\Auth\Domain\Repository\TokenRepositoryInterface;
use MarketPlace\Backoffice\Auth\Domain\ValueObject\JwtTokenId;
use MarketPlace\Backoffice\Auth\Domain\ValueObject\RefreshToken;
use MarketPlace\Backoffice\Auth\Domain\ValueObject\Token;
use MarketPlace\Backoffice\Auth\Infrastructure\Exception\NotGivenClientIdAndSecretForTokenServiceException;
use MarketPlace\Backoffice\Auth\Infrastructure\Exception\UserCredentialsIncorrectException;
use MarketPlace\Backoffice\Auth\Infrastructure\Listener\UserAuthorizedListener;
use MarketPlace\Backoffice\Auth\Infrastructure\Service\TokenService;
use MarketPlace\Common\Domain\Events\EventDispatcher;
use MarketPlace\Common\Domain\Exceptions\UserIsBannedException;
use MarketPlace\Common\Domain\Exceptions\UserIsInactiveException;
use MarketPlace\Common\Domain\ValueObject\Email;
use MarketPlace\Common\Domain\ValueObject\Password;

class AuthorizeService
{
    private array $listeners = [
        UserAuthorizedEvent::class => [
            UserAuthorizedListener::class
        ],
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
     * @throws UniqueTokenIdentifierConstraintViolationException
     * @throws UserCredentialsIncorrectException
     * @throws UserIsInactiveException
     * @throws UserIsBannedException
     * @throws OAuthServerException
     * @throws JsonException
     * @throws NotGivenClientIdAndSecretForTokenServiceException
     */
    public function signIn(SignInDto $dto): Token
    {
        $email = new Email($dto->email);
        $password = new Password($dto->password);

        $user = $this->userAdapter->getByCredentials($email, $password);
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
