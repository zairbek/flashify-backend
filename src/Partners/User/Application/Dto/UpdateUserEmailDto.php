<?php

declare(strict_types=1);

namespace MarketPlace\Partners\User\Application\Dto;

class UpdateUserEmailDto
{
    public function __construct(
        public string $email,
        public ?string $code,
        public ?string $sendAt,
    )
    {
    }
}
