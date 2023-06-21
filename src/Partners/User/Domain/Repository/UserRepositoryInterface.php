<?php

namespace MarketPlace\Partners\User\Domain\Repository;

use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Partners\User\Domain\Entity\Account;
use MarketPlace\Partners\User\Infrastructure\Exception\UserNotFoundException;
use MarketPlace\Partners\User\Infrastructure\Exception\UserUnauthenticatedException;

interface UserRepositoryInterface
{
    public function create(Account $user): void;

    /**
     * @param Uuid $uuid
     * @return Account
     * @throws UserNotFoundException
     */
    public function find(Uuid $uuid): Account;

    /**
     * @return Account
     * @throws UserUnauthenticatedException
     */
    public function me(): Account;
}
