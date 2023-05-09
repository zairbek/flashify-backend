<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\User\Domain\ValueObject;

class UserName
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

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }
}
