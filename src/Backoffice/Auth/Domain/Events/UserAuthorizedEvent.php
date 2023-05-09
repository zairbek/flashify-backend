<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Auth\Domain\Events;

use MarketPlace\Backoffice\Auth\Domain\Entity\User;
use MarketPlace\Common\Domain\Events\EventInterface;

class UserAuthorizedEvent implements EventInterface
{
    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
