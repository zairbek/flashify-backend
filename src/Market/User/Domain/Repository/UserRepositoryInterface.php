<?php

namespace MarketPlace\Market\User\Domain\Repository;

use MarketPlace\Market\User\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function create(User $user): void;
}
