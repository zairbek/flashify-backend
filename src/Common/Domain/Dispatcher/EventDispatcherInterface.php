<?php

namespace MarketPlace\Common\Domain\Dispatcher;

interface EventDispatcherInterface
{
    public function dispatch(array $events): void;
}
