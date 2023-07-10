<?php

declare(strict_types=1);

namespace MarketPlace\Partners\User\Application\Dto;

readonly class UpdateUserNameDto
{
    public function __construct(
        public string $uuid,
        public ?string $firstName,
        public ?string $lastName,
    )
    {
    }
}
