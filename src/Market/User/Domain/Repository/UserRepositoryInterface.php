<?php

namespace MarketPlace\Market\User\Domain\Repository;

use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Market\User\Domain\Entity\User;
use MarketPlace\Market\User\Infrastructure\Exception\UserNotFoundException;
use MarketPlace\Market\User\Infrastructure\Exception\UserUnauthenticatedException;

interface UserRepositoryInterface
{
    public function create(User $user): void;

    /**
     * @param Uuid $uuid
     * @return User
     * @throws UserNotFoundException
     */
    public function find(Uuid $uuid): User;

    /**
     * @return User
     * @throws UserUnauthenticatedException
     */
    public function me(): User;
}
