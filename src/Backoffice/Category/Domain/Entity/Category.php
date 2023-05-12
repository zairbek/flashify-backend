<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Category\Domain\Entity;

use MarketPlace\Common\Domain\ValueObject\ActiveStatus;
use MarketPlace\Common\Domain\ValueObject\CategoryAttribute;
use MarketPlace\Common\Domain\ValueObject\Icon;
use MarketPlace\Common\Domain\ValueObject\Uuid;

class Category
{
    private Uuid $uuid;
    private CategoryAttribute $attribute;
    private ActiveStatus $active;
    private ?Icon $icon = null;
    private ?self $parentCategory = null;

    public function __construct(
        Uuid $uuid,
        CategoryAttribute $attribute,
    )
    {
        $this->uuid = $uuid;
        $this->attribute = $attribute;
        $this->active = ActiveStatus::active();
    }

    public function changeIcon(?Icon $icon): void
    {
        $this->icon = $icon;
    }

    public function changeParentCategory(?self $parentCategory): void
    {
        $this->parentCategory = $parentCategory;
    }

    public function deactivate(): void
    {
        $this->active = ActiveStatus::inactive();
    }

    public function activate(): void
    {
        $this->active = ActiveStatus::active();
    }
    public function getParentCategory(): ?Category
    {
        return $this->parentCategory;
    }

    public function getIcon(): ?Icon
    {
        return $this->icon;
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function getAttribute(): CategoryAttribute
    {
        return $this->attribute;
    }

    public function getActive(): ActiveStatus
    {
        return $this->active;
    }
}
