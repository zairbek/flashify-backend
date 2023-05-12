<?php

namespace MarketPlace\Backoffice\Category\Domain\Repository;

use MarketPlace\Backoffice\Category\Domain\Entity\Category;
use MarketPlace\Backoffice\Category\Infrastructure\Exception\CategoryNotFoundException;
use MarketPlace\Backoffice\Category\Infrastructure\Exception\CategorySlugAlreadyExistsException;
use MarketPlace\Common\Domain\ValueObject\Uuid;

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
}
