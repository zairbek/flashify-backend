<?php

declare(strict_types=1);

namespace MarketPlace\Market\Auth\Domain\Events;

use MarketPlace\Common\Domain\Events\EventInterface;
use MarketPlace\Market\Auth\Domain\Entity\User;

class UserAuthorizedEvent implements EventInterface
{
    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
