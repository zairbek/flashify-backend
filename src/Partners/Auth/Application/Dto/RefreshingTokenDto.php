<?php

declare(strict_types=1);

namespace MarketPlace\Market\Auth\Application\Dto;

class RefreshingTokenDto
{
    public function __construct(
        public string $refreshToken
    )
    {
    }
}
