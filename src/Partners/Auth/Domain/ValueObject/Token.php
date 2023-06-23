<?php

declare(strict_types=1);

namespace MarketPlace\Partners\Auth\Domain\ValueObject;

class Token
{
    private string $accessToken;
    private string $tokenType;
    private string $refreshToken;
    private int $accessTokenLifeTime;

    public function __construct(
        string $accessToken,
        string $tokenType,
        string $refreshToken,
        int $accessTokenLifeTime,
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
     * @return int
     */
    public function getAccessTokenLifeTime(): int
    {
        return $this->accessTokenLifeTime;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->tokenType,
            'lifeTime' => $this->accessTokenLifeTime,
            'accessToken' => $this->accessToken,
            'refreshToken' => $this->refreshToken,
        ];
    }
}
