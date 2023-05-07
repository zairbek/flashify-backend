<?php

declare(strict_types=1);

namespace MarketPlace\Market\Auth\Infrastructure\Repository;

use Laravel\Passport\RefreshTokenRepository as PassportRefreshTokenRepository;
use Laravel\Passport\TokenRepository as PassportTokenRepository;
use MarketPlace\Market\Auth\Domain\Repository\TokenRepositoryInterface;
use MarketPlace\Market\Auth\Domain\ValueObject\JwtTokenId;

class TokenRepository implements TokenRepositoryInterface
{

    public function signOut(JwtTokenId $jwtTokenId): void
    {
        $accessTokenRepo = new PassportTokenRepository();
        $refreshRepo = new PassportRefreshTokenRepository();

        $accessTokenRepo->revokeAccessToken($jwtTokenId->getJwtTokenId());
        $refreshRepo->revokeRefreshTokensByAccessTokenId($jwtTokenId->getJwtTokenId());
    }
}
