<?php

declare(strict_types=1);

namespace MarketPlace\Partners\User\Application\Dto;

class ChangeEmailDto
{
    public function __construct(
        public string $email,
        public string $confirmationCode,
    )
    {
    }
}
