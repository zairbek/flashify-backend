<?php

declare(strict_types=1);

namespace MarketPlace\Partners\Auth\Application\Dto;

class SendCodeForSignInViaEmailDto
{
    public function __construct(
        public string $email
    )
    {
    }
}
