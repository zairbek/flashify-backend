<?php

declare(strict_types=1);

namespace MarketPlace\Market\Category\Domain\Entity;

use MarketPlace\Common\Domain\Entity\AggregateRoot;
use MarketPlace\Common\Domain\Entity\EventTrait;
use MarketPlace\Common\Domain\ValueObject\ActiveStatus;
use MarketPlace\Common\Domain\ValueObject\CategoryAttribute;
use MarketPlace\Common\Domain\ValueObject\Uuid;

class Category implements AggregateRoot
{
    use EventTrait;

    private Uuid $uuid;
    private CategoryAttribute $attribute;
    private ActiveStatus $active;
    private ?Uuid $iconUuid = null;
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

    public function changeAttributes(CategoryAttribute $attribute): void
    {
        $this->attribute = $attribute;
    }

    public function changeIcon(Uuid $icon): void
    {
        $this->iconUuid = $icon;
    }

    public function removeIcon(): void
    {
        $this->iconUuid = null;
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

    public function getIconUuid(): ?Uuid
    {
        return $this->iconUuid;
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

    public function toArray(): array
    {
        return [
            'uuid' => $this->getUuid()->getId(),
            'name' => $this->getAttribute()->getName(),
            'slug' => $this->getAttribute()->getSlug(),
            'description' => $this->getAttribute()->getDescription(),
            'icon_uuid' => $this->getIconUuid()?->getId(),
            'isActive' => $this->getActive()->isActive(),
            'parentCategory' => $this->getParentCategory()?->toArray(),
        ];
    }
}
