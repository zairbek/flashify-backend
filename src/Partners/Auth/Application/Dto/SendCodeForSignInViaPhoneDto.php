<?php

declare(strict_types=1);

namespace MarketPlace\Partners\Auth\Application\Dto;

class SendCodeForSignInViaPhoneDto
{
    public function __construct(
        public string $regionIsoCode,
        public string $phone
    )
    {
    }
}
