<?php

declare(strict_types=1);

namespace MarketPlace\Partners\Auth\Application\Dto;

class SignInWithEmailDto
{
    public function __construct(
        public string $email,
        public string $confirmationCode,
    )
    {
    }
}
