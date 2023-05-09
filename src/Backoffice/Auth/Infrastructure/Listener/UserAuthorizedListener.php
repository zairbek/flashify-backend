<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Auth\Infrastructure\Listener;

use MarketPlace\Market\Auth\Domain\Events\UserAuthorizedEvent;

class UserAuthorizedListener
{
    public function __invoke(UserAuthorizedEvent $event): void
    {
        \Log::info('user logged: ' . $event->user->getId()->getId());
    }
}
