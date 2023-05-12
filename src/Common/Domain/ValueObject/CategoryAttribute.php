<?php

declare(strict_types=1);

namespace MarketPlace\Common\Domain\ValueObject;

class CategoryAttribute
{
    private string $name;
    private string $slug;
    private ?string $description;

    public function __construct(string $name, ?string $slug = null, ?string $description = null)
    {
        $this->name = $name;
        $this->slug = $slug ?: \Str::slug($name);
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
}
