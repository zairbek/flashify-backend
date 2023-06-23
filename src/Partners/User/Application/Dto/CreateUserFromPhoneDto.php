<?php

declare(strict_types=1);

namespace MarketPlace\Market\User\Application\Dto;

class CreateUserFromPhoneDto
{
    public function __construct(
        public string $uuid,
        public string $login,
        public string $phoneUuid,
    )
    {
    }
}
