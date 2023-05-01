<?php

declare(strict_types=1);

namespace MarketPlace\Market\Auth\Application\Dto;

class SendCodeForSignInViaEmailDto
{
    public function __construct(
        public string $email
    )
    {
    }
}
