<?php

declare(strict_types=1);

namespace MarketPlace\Market\Auth\Domain\Events;

use App;
use MarketPlace\Common\Domain\Events\EventInterface;
use MarketPlace\Common\Domain\ValueObject\ConfirmationCode;
use MarketPlace\Common\Domain\ValueObject\Phone;

class SendConfirmationCodeForPhoneNumberEvent implements EventInterface
{
    public Phone $phone;
    public ConfirmationCode $confirmationCode;
    public function __construct(Phone $phone, ConfirmationCode $confirmationCode)
    {
        $this->phone = $phone;
        $this->confirmationCode = $confirmationCode;
    }
}
