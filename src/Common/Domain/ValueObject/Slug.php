<?php

declare(strict_types=1);

namespace MarketPlace\Common\Domain\ValueObject;

class Slug
{
    private string $slug;

    public function __construct(string $slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }
}
