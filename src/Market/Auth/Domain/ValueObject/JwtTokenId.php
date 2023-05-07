<?php

declare(strict_types=1);

namespace MarketPlace\Market\Auth\Domain\ValueObject;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtTokenId
{
    private string $jwtTokenId;

    public function __construct(string $jwtTokenId)
    {
        $this->jwtTokenId = $jwtTokenId;
    }

    public static function fromJwtToken(string $jwtToken): self
    {
        $key = new Key(config('passport.public_key'), 'RS256');

        $jwt = (array) JWT::decode($jwtToken, $key);

        return new self($jwt['jti']);
    }

    /**
     * @return string
     */
    public function getJwtTokenId(): string
    {
        return $this->jwtTokenId;
    }
}
