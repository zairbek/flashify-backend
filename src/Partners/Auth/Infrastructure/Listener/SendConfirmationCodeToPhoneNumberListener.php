<?php

declare(strict_types=1);

namespace MarketPlace\Partners\Auth\Infrastructure\Listener;

use JetBrains\PhpStorm\NoReturn;
use MarketPlace\Market\Auth\Domain\Events\SendConfirmationCodeForPhoneNumberEvent;

class SendConfirmationCodeToPhoneNumberListener
{
    public function __invoke(SendConfirmationCodeForPhoneNumberEvent $event): void
    {
        info('send code : ' . $event->confirmationCode->getCode() . ' to ' . $event->phone->getInternationalFormat());
    }
}
