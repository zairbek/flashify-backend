<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Digest\Icon\Domain\ValueObject;

class IconName
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
