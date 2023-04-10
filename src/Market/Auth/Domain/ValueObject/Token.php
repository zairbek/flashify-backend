<?php

declare(strict_types=1);

namespace MarketPlace\Market\Auth\Domain\ValueObject;

class Token
{
    private string $accessToken;
    private string $tokenType;
    private string $refreshToken;
    private string $accessTokenLifeTime;

    public function __construct(
        string $accessToken,
        string $tokenType,
        string $refreshToken,
        string $accessTokenLifeTime,
    )
    {
        $this->accessToken = $accessToken;
        $this->tokenType = $tokenType;
        $this->refreshToken = $refreshToken;
        $this->accessTokenLifeTime = $accessTokenLifeTime;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @return string
     */
    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * @return string
     */
    public function getAccessTokenLifeTime(): string
    {
        return $this->accessTokenLifeTime;
    }
}
