<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Auth\Application\Dto;

class SignOutDto
{
    public function __construct(
        public string $bearerToken
    )
    {
    }
}
