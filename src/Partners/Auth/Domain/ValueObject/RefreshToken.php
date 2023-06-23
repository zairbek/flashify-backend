<?php

declare(strict_types=1);

namespace MarketPlace\Partners\Auth\Domain\ValueObject;

class RefreshToken
{
    public function __construct(
        private string $refreshToken
    )
    {
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }
}
