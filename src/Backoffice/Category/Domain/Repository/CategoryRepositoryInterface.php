<?php

namespace MarketPlace\Backoffice\Category\Domain\Repository;

use MarketPlace\Backoffice\Category\Application\Dto\GetCategoryDto;
use MarketPlace\Backoffice\Category\Domain\Entity\Category;
use MarketPlace\Backoffice\Category\Infrastructure\Exception\CategoryNotFoundException;
use MarketPlace\Backoffice\Category\Infrastructure\Exception\CategorySlugAlreadyExistsException;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Common\Infrastructure\Service\Collection;

interface CategoryRepositoryInterface
{

    /**
     * @param Category $category
     * @return void
     * @throws CategorySlugAlreadyExistsException
     */
    public function create(Category $category): void;

    /**
     * @param Uuid $uuid
     * @return Category
     * @throws CategoryNotFoundException
     */
    public function find(Uuid $uuid): Category;

    public function get(GetCategoryDto $dto): Collection;

    /**
     * @throws CategorySlugAlreadyExistsException
     */
    public function update(Category $category): void;
}
