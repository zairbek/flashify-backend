<?php

declare(strict_types=1);

namespace MarketPlace\Partners\Auth\Domain\Events;

use MarketPlace\Common\Domain\Events\EventInterface;
use MarketPlace\Partners\Auth\Domain\ValueObject\Phone;

class SendConfirmationCodeForPhoneNumberEvent implements EventInterface
{
    public Phone $phone;
    public function __construct(Phone $phone)
    {
        $this->phone = $phone;
    }
}
