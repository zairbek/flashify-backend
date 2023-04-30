<?php

declare(strict_types=1);

namespace MarketPlace\Market\User\Infrastructure\Repository;

use MarketPlace\Market\User\Domain\Entity\User;
use MarketPlace\Market\User\Domain\Repository\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{

    public function create(User $user): void
    {
        \App\Models\User::create([
            'uuid' => $user->getUuid()->getId(),
            'login' => $user->getLogin()->getLogin(),
        ]);
    }
}
