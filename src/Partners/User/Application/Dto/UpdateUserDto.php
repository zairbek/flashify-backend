<?php

declare(strict_types=1);

namespace MarketPlace\Partners\User\Application\Dto;

class UpdateUserDto
{
    public function __construct(
        public string $uuid,
        public string $login,
        public ?UpdateUserPhoneDto $phoneDto,
        public ?UpdateUserEmailDto $emailDto,
        public ?string $firstName,
        public ?string $lastName,
        public string $status,
    )
    {
    }
}
