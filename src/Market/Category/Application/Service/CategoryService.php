<?php

declare(strict_types=1);

namespace MarketPlace\Market\Category\Application\Service;

use MarketPlace\Common\Domain\ValueObject\CategoryAttribute;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Common\Infrastructure\Service\Collection;
use MarketPlace\Market\Category\Application\Dto\CreateCategoryDto;
use MarketPlace\Market\Category\Application\Dto\GetCategoryDto;
use MarketPlace\Market\Category\Application\Dto\UpdateCategoryDto;
use MarketPlace\Market\Category\Domain\Entity\Category;
use MarketPlace\Market\Category\Domain\Entity\CategoryIcon;
use MarketPlace\Market\Category\Domain\Repository\CategoryRepositoryInterface;
use MarketPlace\Market\Category\Infrastructure\Adapter\IconAdapter;
use MarketPlace\Market\Category\Infrastructure\Exception\CategoryIconNotFoundException;
use MarketPlace\Market\Category\Infrastructure\Exception\CategoryNotFoundException;
use MarketPlace\Market\Category\Infrastructure\Exception\CategorySlugAlreadyExistsException;

class CategoryService
{
    private CategoryRepositoryInterface $repository;
    private IconAdapter $iconAdapter;

    public function __construct(CategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->iconAdapter = new IconAdapter();
    }

    /**
     * @throws CategoryNotFoundException
     * @throws CategorySlugAlreadyExistsException
     * @throws CategoryIconNotFoundException
     */
    public function createCategory(CreateCategoryDto $dto): void
    {
        $category = new Category(
            uuid: Uuid::next(),
            attribute: new CategoryAttribute(
                name: $dto->name,
                slug: $dto->slug,
                description: $dto->description
            )
        );
        if (! is_null($dto->parentCategory)) {
            $parentCategory = $this->repository->find(new Uuid($dto->parentCategory));
            $category->changeParentCategory($parentCategory);
        }

        if ($dto->icon) {
            $icon = $this->iconAdapter->findByUuid(new Uuid($dto->icon));
            $category->changeIcon($icon->getUuid());
        } else {
            $category->removeIcon();
        }

        if ($dto->active) {
            $category->activate();
        } else {
            $category->deactivate();
        }

        $this->repository->create($category);
    }

    /**
     * @throws CategoryNotFoundException
     * @throws CategorySlugAlreadyExistsException
     * @throws CategoryIconNotFoundException
     */
    public function updateCategory(UpdateCategoryDto $dto): void
    {
        $category = $this->repository->find(new Uuid($dto->uuid));

        $category->changeAttributes(new CategoryAttribute(
            name: $dto->name, slug: $dto->slug, description: $dto->description
        ));

        if ($dto->iconUuid) {
            $this->iconAdapter->findByUuid(new Uuid($dto->iconUuid));
            $category->changeIcon(new Uuid($dto->iconUuid));
        }
        if ($dto->parentCategory) {
            $parentCategory = $this->repository->find(new Uuid($dto->parentCategory));
            $category->changeParentCategory($parentCategory);
        }
        if ($dto->active) {
            $category->activate();
        } else {
            $category->deactivate();
        }

        $this->repository->update($category);
    }

    /**
     * @throws CategoryNotFoundException
     */
    public function deleteCategory(Uuid $uuid): void
    {
        $category = $this->repository->find($uuid);

        $this->repository->delete($category);
    }

    /**
     * @throws CategoryNotFoundException
     */
    public function showCategory(string $uuid): Category
    {
        return $this->repository->find(new Uuid($uuid));
    }

    /**
     * @param GetCategoryDto $dto
     * @return Collection<Category>
     */
    public function getCategories(GetCategoryDto $dto): Collection
    {
        $categories = $this->repository->get($dto);

        $iconUuids = $categories->map(fn (Category $category) => $category->getIconUuid())
            ->filter(fn ($item) => $item)->unique();
        $icons = $this->iconAdapter->getByUuids($iconUuids);

        return $categories->transform(function (Category $category) use ($icons) {
            $icon = null;
            if ($category->getIconUuid()) {
                $icon = $icons
                    ->filter(fn(CategoryIcon $categoryIcon) => $categoryIcon->getUuid()->isEqualTo($category->getIconUuid()))
                    ->first();
            }

            return $this->toArray($category, $icon);
        });
    }

    private function toArray(Category $category, ?CategoryIcon $categoryIcon = null): array
    {
        return [
            'uuid' => $category->getUuid()->getId(),
            'name' => $category->getAttribute()->getName(),
            'slug' => $category->getAttribute()->getSlug(),
            'description' => $category->getAttribute()->getDescription(),
            'parentCategory' => $category->getParentCategory()
                ? $this->toArray($category->getParentCategory())
                : null,
            'isActive' => $category->getActive()->isActive(),
            'icon' => $categoryIcon
                ? [
                    'uuid' => $categoryIcon->getUuid()->getId(),
                    'name' => $categoryIcon->getName()->getName(),
                    'file' => $categoryIcon->getUrl()->getUrl(),
                ]
                : null
        ];
    }
}
