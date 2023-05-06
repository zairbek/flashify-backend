<?php

declare(strict_types=1);

namespace MarketPlace\Market\Auth\Infrastructure\Service;

use App;
use DateTimeImmutable;
use Error;
use Exception;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use GuzzleHttp\Psr7\ServerRequest as GuzzleRequest;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use JsonException;
use Laravel\Passport\Bridge\AccessToken;
use Laravel\Passport\Bridge\AccessTokenRepository;
use Laravel\Passport\Bridge\Client;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use Laravel\Passport\Events\AccessTokenCreated;
use Laravel\Passport\Passport;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\ResponseTypes\BearerTokenResponse;
use MarketPlace\Market\Auth\Domain\Entity\User;
use MarketPlace\Market\Auth\Domain\ValueObject\RefreshToken;
use MarketPlace\Market\Auth\Domain\ValueObject\Token;
use MarketPlace\Market\Auth\Infrastructure\Exception\NotGivenClientIdAndSecretForTokenServiceException;
use Psr\Http\Message\ResponseInterface;
use TypeError;

class TokenService
{
    private AuthorizationServer $server;
    private string|null $clientId;
    private string|null $clientSecret;

    /**
     * @throws NotGivenClientIdAndSecretForTokenServiceException
     */
    public function __construct()
    {
        $this->server = App::make(AuthorizationServer::class);
        /** @var Request $request */
        $request = App::get('request');

        $clientId = $request->header('client-id');
        $clientSecret = $request->header('client-secret');

        if (is_null($clientId) || is_null($clientSecret)) {
            throw new NotGivenClientIdAndSecretForTokenServiceException();
        }

        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }


    /**
     * @param User $user
     * @return Token
     * @throws OAuthServerException
     * @throws UniqueTokenIdentifierConstraintViolationException
     * @throws JsonException
     */
    public function generate(User $user): Token
    {
        $passportToken = $this->createPassportTokenByUser($user, $this->clientId);
        $bearerToken = $this->sendBearerTokenResponse($passportToken['access_token'], $passportToken['refresh_token']);
        $tokens = json_decode($bearerToken->getBody()->__toString(), true, 512, JSON_THROW_ON_ERROR);

        return new Token(
            accessToken: $tokens['access_token'],
            tokenType: $tokens['token_type'],
            refreshToken: $tokens['refresh_token'],
            accessTokenLifeTime: $tokens['expires_in']
        );
    }

    /**
     * @throws OAuthServerException
     * @throws JsonException
     */
    public function refreshing(RefreshToken $refreshToken): Token
    {
        $psrResponse = $this
            ->server
            ->respondToAccessTokenRequest(
                (new GuzzleRequest('POST', ''))
                    ->withParsedBody([
                        'grant_type' => 'refresh_token',
                        'refresh_token' => $refreshToken->getRefreshToken(),
                        'client_id' => $this->clientId,
                        'client_secret' => $this->clientSecret,
                        'scope' => '',
                    ]),
                new GuzzleResponse()
            )
        ;

        $tokens = json_decode((string)$psrResponse->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return new Token(
            accessToken: $tokens['access_token'],
            tokenType: $tokens['token_type'],
            refreshToken: $tokens['refresh_token'],
            accessTokenLifeTime: $tokens['expires_in']
        );
    }

    /**
     * @param User $user
     * @param $clientId
     * @return array
     * @throws OAuthServerException
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    private function createPassportTokenByUser(User $user, $clientId): array
    {
        $client = new Client($clientId, null, null);
        $accessToken = new AccessToken($user->getId()->getId(), [], $client);
        $accessToken->setIdentifier($this->generateUniqueIdentifier());
        $privateKey = new CryptKey(config('passport.private_key'), null, false);
        $accessToken->setPrivateKey($privateKey);
        $accessToken->setExpiryDateTime((new DateTimeImmutable())->add(Passport::tokensExpireIn()));

        $dispatch = app(\Illuminate\Contracts\Events\Dispatcher::class);
        $dispatch->dispatch(new AccessTokenCreated(
            $accessToken->getIdentifier(),
            $accessToken->getUserIdentifier(),
            $accessToken->getClient()->getIdentifier()
        ));

        $accessTokenRepository = new AccessTokenRepository(new TokenRepository(), new Dispatcher());
        $accessTokenRepository->persistNewAccessToken($accessToken);
        $refreshToken = $this->issueRefreshToken($accessToken);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ];
    }

    /**
     * @param $accessToken
     * @param $refreshToken
     * @return ResponseInterface
     */
    private function sendBearerTokenResponse($accessToken, $refreshToken): ResponseInterface
    {
        $response = new BearerTokenResponse();
        $response->setAccessToken($accessToken);
        $response->setRefreshToken($refreshToken);
        $response->setEncryptionKey(app('encrypter')->getKey());
        return $response->generateHttpResponse(new GuzzleResponse());
    }

    /**
     * @throws UniqueTokenIdentifierConstraintViolationException
     * @throws OAuthServerException
     */
    private function issueRefreshToken(AccessTokenEntityInterface $accessToken)
    {
        $maxGenerationAttempts = 10;
        $refreshTokenRepository = app(RefreshTokenRepository::class);

        $refreshToken = $refreshTokenRepository->getNewRefreshToken();
        if (is_null($refreshToken)) {
            throw OAuthServerException::serverError('Refresh token is null from RefreshTokenRepository');
        }
        $refreshToken->setExpiryDateTime((new DateTimeImmutable())->add(Passport::refreshTokensExpireIn()));
        $refreshToken->setAccessToken($accessToken);

        while ($maxGenerationAttempts-- > 0) {
            $refreshToken->setIdentifier($this->generateUniqueIdentifier());
            try {
                $refreshTokenRepository->persistNewRefreshToken($refreshToken);

                return $refreshToken;
            } catch (UniqueTokenIdentifierConstraintViolationException $e) {
                if ($maxGenerationAttempts === 0) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Generate a new unique identifier.gs
     *
     * @param int $length
     * @throws OAuthServerException
     * @return string
     */
    private function generateUniqueIdentifier(int $length = 40): string
    {
        try {
            return bin2hex(random_bytes($length));
        } catch (TypeError | Error $e) {
            throw OAuthServerException::serverError('An unexpected error has occurred');
        } catch (Exception $e) {
            // If you get this message, the CSPRNG failed hard.
            throw OAuthServerException::serverError('Could not generate a random string');
        }
    }
}
