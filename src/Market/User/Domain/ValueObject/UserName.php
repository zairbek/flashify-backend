<?php

declare(strict_types=1);

namespace MarketPlace\Market\User\Domain\ValueObject;

class UserName
{
    private ?string $firstName;
    private ?string $lastName;
    private ?string $middleName;

    public function __construct(
        ?string $firstName,
        ?string $lastName,
        ?string $middleName,
    )
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->middleName = $middleName;
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

    /**
     * @return string|null
     */
    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }
}
