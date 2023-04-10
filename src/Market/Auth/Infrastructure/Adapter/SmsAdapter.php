<?php

declare(strict_types=1);

namespace MarketPlace\Market\Auth\Infrastructure\Adapter;

use MarketPlace\Common\Domain\ValueObject\Phone;

class SmsAdapter implements \MarketPlace\Market\Auth\Domain\Adapter\SmsAdapterInterface
{

    public function sendCode(Phone $phone): void
    {
        \Log::info('SMS: to: ' . $phone);
    }
}
