<?php

declare(strict_types=1);

namespace MarketPlace\Common\Domain\ValueObject;

class Icon
{
    private string $icon;

    public function __construct(string $icon)
    {
        $this->icon = $icon;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }
}
