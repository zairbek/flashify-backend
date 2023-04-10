<?php

namespace MarketPlace\Common\Domain\Events;

interface EventInterface
{
    public function execute(): void;
}
