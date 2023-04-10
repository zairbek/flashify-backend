<?php

declare(strict_types=1);

namespace MarketPlace\Common\Domain\ValueObject;

class PersonName
{
    public function __construct(
        private ?string $firstName = null,
        private ?string $lastName = null,
        private ?string $middleName = null,
    )
    {
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
