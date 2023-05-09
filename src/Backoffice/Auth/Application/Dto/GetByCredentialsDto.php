<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Auth\Application\Dto;

class GetByCredentialsDto
{
    public function __construct(
        public string $email,
        public string $password,
    )
    {
    }
}
