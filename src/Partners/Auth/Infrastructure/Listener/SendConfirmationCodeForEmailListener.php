<?php

declare(strict_types=1);

namespace MarketPlace\Partners\Auth\Infrastructure\Listener;

use MarketPlace\Market\Auth\Domain\Events\SendConfirmationCodeForEmailEvent;

class SendConfirmationCodeForEmailListener
{
    public function __invoke(SendConfirmationCodeForEmailEvent $event): void
    {
        info('email code ' . $event->confirmationCode->getCode());
    }
}
