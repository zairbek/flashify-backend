<?php

declare(strict_types=1);

namespace MarketPlace\Market\User\Application\Dto;

class UpdateUserEmailDto
{
    public function __construct(
        public string $uuid,
        public string $email,
        public string $userUuid,
        public ?string $confirmationCode,
        public ?string $sendAt,
        public ?string $verifiedAt,
    )
    {
    }
}
