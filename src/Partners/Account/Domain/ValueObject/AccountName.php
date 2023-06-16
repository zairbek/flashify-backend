<?php

declare(strict_types=1);

namespace MarketPlace\Partners\Account\Domain\ValueObject;

class AccountName
{
    private ?string $firstName;
    private ?string $lastName;

    public function __construct(
        ?string $firstName,
        ?string $lastName,
    )
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }
}
