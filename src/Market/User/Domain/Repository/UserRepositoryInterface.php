<?php

namespace MarketPlace\Market\User\Domain\Repository;

use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Market\User\Domain\Entity\User;
use MarketPlace\Market\User\Infrastructure\Exception\UserNotFoundException;

interface UserRepositoryInterface
{
    public function create(User $user): void;

    /**
     * @param Uuid $uuid
     * @return User
     * @throws UserNotFoundException
     */
    public function find(Uuid $uuid): User;
}
