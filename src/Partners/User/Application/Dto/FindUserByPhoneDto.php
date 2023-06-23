<?php

declare(strict_types=1);

namespace MarketPlace\Partners\User\Application\Dto;

class FindUserByPhoneDto
{
    public string $regionIsoCode;
    public string $phoneNumber;

    public function __construct(string $regionIsoCode, string $phoneNumber)
    {
        $this->regionIsoCode = $regionIsoCode;
        $this->phoneNumber = $phoneNumber;
    }
}
