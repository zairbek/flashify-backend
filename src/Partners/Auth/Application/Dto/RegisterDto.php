<?php

declare(strict_types=1);

namespace MarketPlace\Partners\Auth\Application\Dto;

class RegisterDto
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $phoneRegionCode,
        public string $phone,
        public string $code,
        public string $password,
    )
    {
    }
}
