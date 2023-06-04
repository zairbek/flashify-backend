<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Category\Domain\ValueObject;

class Name
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
