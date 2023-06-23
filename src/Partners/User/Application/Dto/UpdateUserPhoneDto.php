<?php

declare(strict_types=1);

namespace MarketPlace\Partners\User\Application\Dto;

class UpdateUserPhoneDto
{
    public function __construct(
        public string $regionCode,
        public string $phone,
        public ?string $code,
        public ?string $sendAt,
    )
    {
    }
}
