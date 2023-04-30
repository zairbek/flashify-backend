<?php

declare(strict_types=1);

namespace MarketPlace\Market\User\Application\Dto;

class FindUserPhoneDto
{
    public string $regionIsoCode;
    public string $phoneNumber;

    public function __construct(string $regionIsoCode, string $phoneNumber)
    {
        $this->regionIsoCode = $regionIsoCode;
        $this->phoneNumber = $phoneNumber;
    }
}
