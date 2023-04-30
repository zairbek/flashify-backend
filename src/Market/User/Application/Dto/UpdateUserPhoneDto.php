<?php

declare(strict_types=1);

namespace MarketPlace\Market\User\Application\Dto;

class UpdateUserPhoneDto
{
    public function __construct(
        public string $uuid,
        public string $regionIsoCode,
        public string $phoneNumber,
        public string $createdAt,
        public ?string $userId,
        public ?string $confirmationCode,
        public ?string $sendAt,
    )
    {
    }
}
