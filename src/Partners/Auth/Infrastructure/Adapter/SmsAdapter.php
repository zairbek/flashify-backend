<?php

declare(strict_types=1);

namespace MarketPlace\Partners\Auth\Infrastructure\Adapter;

use MarketPlace\Common\Domain\ValueObject\Phone;
use MarketPlace\Partners\Auth\Domain\Adapter\SmsAdapterInterface;

class SmsAdapter implements SmsAdapterInterface
{

    public function sendCode(Phone $phone): void
    {
        \Log::info('SMS: to: ' . $phone);
    }
}
