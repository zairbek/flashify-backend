<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Category\Infrastructure\Repository;

use App\Models\Category as CategoryModel;
use MarketPlace\Backoffice\Category\Application\Dto\GetCategoryDto;
use MarketPlace\Backoffice\Category\Domain\Entity\Category;
use MarketPlace\Backoffice\Category\Domain\Repository\CategoryRepositoryInterface;
use MarketPlace\Backoffice\Category\Infrastructure\Exception\CategoryNotFoundException;
use MarketPlace\Backoffice\Category\Infrastructure\Exception\CategorySlugAlreadyExistsException;
use MarketPlace\Common\Domain\ValueObject\ActiveStatus;
use MarketPlace\Common\Domain\ValueObject\CategoryAttribute;
use MarketPlace\Common\Domain\ValueObject\Icon;
use MarketPlace\Common\Domain\ValueObject\Slug;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Common\Infrastructure\Service\Collection;
use MarketPlace\Common\Infrastructure\Service\Hydrator;

class CategoryRepository implements CategoryRepositoryInterface
{

    private Hydrator $hydrator;

    public function __construct()
    {
        $this->hydrator = new Hydrator();
    }

    /**
     * @throws CategorySlugAlreadyExistsException
     */
    public function create(Category $category): void
    {
        if ($this->existsSlug(new Slug($category->getAttribute()->getSlug()))) {
            throw new CategorySlugAlreadyExistsException();
        }

        CategoryModel::create([
            'uuid' => $category->getUuid()->getId(),
            'name' => $category->getAttribute()->getName(),
            'slug' => $category->getAttribute()->getSlug(),
            'description' => $category->getAttribute()->getDescription(),
            'parent_uuid' => $category->getParentCategory()?->getUuid()->getId(),
            'active' => $category->getActive()->isStatus(),
            'icon' => $category->getIcon()?->getIcon()
        ]);
    }

    /**
     * @throws CategorySlugAlreadyExistsException
     */
    public function update(Category $category): void
    {
        if ($this->existsSlugWithout(new Slug($category->getAttribute()->getSlug()), new Uuid($category->getUuid()->getId()))) {
            throw new CategorySlugAlreadyExistsException();
        }

        /** @var CategoryModel $categoryModel */
        $categoryModel = CategoryModel::where('uuid', $category->getUuid()->getId())->first();
        $categoryModel->update([
            'name' => $category->getAttribute()->getName(),
            'slug' => $category->getAttribute()->getSlug(),
            'description' => $category->getAttribute()->getDescription(),
            'parent_uuid' => $category->getParentCategory()?->getUuid()->getId(),
            'active' => $category->getActive()->isStatus(),
            'icon' => $category->getIcon()?->getIcon()
        ]);
    }

    public function delete(Category $category): void
    {
        CategoryModel::where('uuid', $category->getUuid()->getId())->delete();
    }

    /**
     * @throws CategoryNotFoundException
     */
    public function find(Uuid $uuid): Category
    {
        /** @var CategoryModel $categoryModel */
        $categoryModel = CategoryModel::query()->where('uuid', $uuid->getId())->first();

        if (is_null($categoryModel)) {
            throw new CategoryNotFoundException();
        }

        return $this->categoryHydrator($categoryModel);
    }

    /**
     * @throws CategoryNotFoundException
     */
    public function findBySlug(Slug $slug): Category
    {
        /** @var CategoryModel $categoryModel */
        $categoryModel = CategoryModel::query()->where('slug', $slug->getSlug())->first();

        if (is_null($categoryModel)) {
            throw new CategoryNotFoundException();
        }

        return $this->categoryHydrator($categoryModel);
    }

    private function existsSlug(Slug $slug): bool
    {
        return CategoryModel::query()->where('slug', $slug->getSlug())->exists();
    }

    private function existsSlugWithout(Slug $slug, Uuid $uuid): bool
    {
        return CategoryModel::query()
            ->whereNot('uuid', $uuid->getId())
            ->where('slug', $slug->getSlug())
            ->exists();
    }

    public function get(GetCategoryDto $dto): Collection
    {
        $query = CategoryModel::query()->with('parent');

        if ($dto->parentUuid) {
            $query->where('parent_uuid', $dto->parentUuid);
        } else {
            $query->whereNull('parent_uuid');
        }

        $count = $query->count();

        if ($dto->search) {
            $query->where('name', 'like', "%$dto->search%");
        }

        $query->orderBy($dto->sortField, $dto->sortDirection);
        $query->offset($dto->offset ?? 0);
        $query->limit($dto->limit ?? 10);

        $result = $query->get();

        return (new Collection($result))->map(function (CategoryModel $category) {
            return $this->categoryHydrator($category);
        })
            ->setTotal($count)
            ->setLimit($dto->limit)
            ->setOffset($dto->offset)
            ->setAdditional([
                'parent' => $result->first()?->parent?->parent?->uuid,
                'current' => $dto->parentUuid
            ])
        ;
    }

    private function categoryHydrator(CategoryModel $categoryModel): Category
    {
        return $this->hydrator->hydrate(Category::class, [
            'uuid' => new Uuid($categoryModel->uuid),
            'attribute' => new CategoryAttribute(
                name: $categoryModel->name,
                slug: $categoryModel->slug,
                description: $categoryModel->description,
            ),
            'active' => new ActiveStatus($categoryModel->active),
            'icon' => $categoryModel->icon ? new Icon($categoryModel->icon) : null,
            'parentCategory' => $categoryModel->parent
                ? $this->categoryHydrator($categoryModel->parent)
                : null
        ]);
    }
}
