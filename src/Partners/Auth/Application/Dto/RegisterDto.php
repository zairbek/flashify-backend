<?php

declare(strict_types=1);

namespace MarketPlace\Partners\Auth\Application\Dto;

class RegisterDto
{
    public function __construct(
        public string $phoneRegionCode,
        public string $phone,
        public string $code,
    )
    {
    }
}
