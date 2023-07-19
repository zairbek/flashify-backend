<?php

namespace MarketPlace\Market\Category\Domain\Repository;

use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Common\Infrastructure\Service\Collection;
use MarketPlace\Market\Category\Application\Dto\GetCategoryDto;
use MarketPlace\Market\Category\Domain\Entity\Category;
use MarketPlace\Market\Category\Infrastructure\Exception\CategoryNotFoundException;
use MarketPlace\Market\Category\Infrastructure\Exception\CategorySlugAlreadyExistsException;

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

    public function delete(Category $category): void;
}
