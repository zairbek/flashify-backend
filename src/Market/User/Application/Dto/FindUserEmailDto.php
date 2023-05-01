<?php

declare(strict_types=1);

namespace MarketPlace\Market\User\Application\Dto;

class FindUserEmailDto
{
    public function __construct(
        public string $email
    )
    {
    }
}
