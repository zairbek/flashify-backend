<?php

declare(strict_types=1);

namespace MarketPlace\Market\Auth\Domain\Events;

use App;
use MarketPlace\Common\Domain\Events\EventInterface;
use MarketPlace\Common\Domain\ValueObject\ConfirmationCode;
use MarketPlace\Common\Domain\ValueObject\Phone;
use MarketPlace\Market\Auth\Domain\Adapter\SmsAdapterInterface;

class SendConfirmationCodeForPhoneNumberEvent implements EventInterface
{
    private Phone $phone;
    private ConfirmationCode $confirmationCode;
    public function __construct(Phone $phone, ConfirmationCode $confirmationCode)
    {
        $this->phone = $phone;
        $this->confirmationCode = $confirmationCode;
    }

    public function execute(): void
    {
        $smsAdapter = App::get(SmsAdapterInterface::class);

        \Log::info('PHONE NUMBER CONFIRMATION CODE: ' . $this->phone->getInternationalFormat()  .' - '. $this->confirmationCode->getCode());
    }
}
