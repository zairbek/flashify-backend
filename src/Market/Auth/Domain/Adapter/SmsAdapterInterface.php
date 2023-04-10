<?php

namespace MarketPlace\Market\Auth\Domain\Adapter;

use MarketPlace\Common\Domain\ValueObject\Phone;

interface SmsAdapterInterface
{
    public function sendCode(Phone $phone): void;
}
