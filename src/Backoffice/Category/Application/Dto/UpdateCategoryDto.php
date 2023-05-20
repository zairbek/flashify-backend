<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Category\Application\Dto;

class UpdateCategoryDto
{
    public function __construct(
        public string $uuid,
        public string $name,
        public string $slug,
        public ?string $description = null,
        public ?string $parentCategory = null,
        public ?bool $active = true,
        public ?string $icon = null,
    )
    {
    }
}
