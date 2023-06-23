<?php

declare(strict_types=1);

namespace MarketPlace\Partners\Auth\Application\Dto;

class SignInWithPhoneDto
{
    public function __construct(
        public string $regionIsoCode,
        public string $phone,
        public string $confirmationCode,
    )
    {
    }
}
