<?php

namespace MarketPlace\Partners\Account\Domain\Repository;

use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Partners\Account\Domain\Entity\Account;
use MarketPlace\Partners\Account\Infrastructure\Exception\AccountNotFoundException;
use MarketPlace\Partners\Account\Infrastructure\Exception\AccountUnauthenticatedException;

interface AccountRepositoryInterface
{
    public function create(Account $user): void;

    /**
     * @param Uuid $uuid
     * @return Account
     * @throws AccountNotFoundException
     */
    public function find(Uuid $uuid): Account;

    /**
     * @return Account
     * @throws AccountUnauthenticatedException
     */
    public function me(): Account;
}
