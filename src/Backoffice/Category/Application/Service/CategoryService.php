<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Category\Application\Service;

use MarketPlace\Backoffice\Category\Application\Dto\CreateCategoryDto;
use MarketPlace\Backoffice\Category\Application\Dto\GetCategoryDto;
use MarketPlace\Backoffice\Category\Domain\Entity\Category;
use MarketPlace\Backoffice\Category\Domain\Repository\CategoryRepositoryInterface;
use MarketPlace\Backoffice\Category\Infrastructure\Exception\CategoryNotFoundException;
use MarketPlace\Backoffice\Category\Infrastructure\Exception\CategorySlugAlreadyExistsException;
use MarketPlace\Common\Domain\ValueObject\CategoryAttribute;
use MarketPlace\Common\Domain\ValueObject\Icon;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Common\Infrastructure\Service\Collection;

class CategoryService
{
    private CategoryRepositoryInterface $repository;

    public function __construct(CategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @throws CategoryNotFoundException
     * @throws CategorySlugAlreadyExistsException
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
        $category->changeIcon($dto->icon ? new Icon($dto->icon) : null);
        if ($dto->active) {
            $category->activate();
        } else {
            $category->deactivate();
        }

        $this->repository->create($category);
    }

    /**
     * @param GetCategoryDto $dto
     * @return Collection<Category>
     */
    public function getCategories(GetCategoryDto $dto): Collection
    {
        return $this->repository->get($dto);
    }
}
