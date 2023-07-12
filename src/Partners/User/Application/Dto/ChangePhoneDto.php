<?php

declare(strict_types=1);

namespace MarketPlace\Partners\User\Application\Dto;

class ChangePhoneDto
{
    public function __construct(
        public string $regionCode,
        public string $number,
        public string $confirmationCode,
    )
    {
    }
}
