<?php

declare(strict_types=1);

namespace MarketPlace\Market\Category\Application\Dto;

class CreateCategoryDto
{
    public function __construct(
        public string $name,
        public ?string $slug = null,
        public ?string $description = null,
        public ?string $parentCategory = null,
        public ?bool $active = true,
        public ?string $icon = null,
    )
    {
    }
}
