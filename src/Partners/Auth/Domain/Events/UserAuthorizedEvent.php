<?php

declare(strict_types=1);

namespace MarketPlace\Partners\Auth\Domain\Events;

use MarketPlace\Common\Domain\Events\EventInterface;
use MarketPlace\Partners\Auth\Domain\Entity\User;

class UserAuthorizedEvent implements EventInterface
{
    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
