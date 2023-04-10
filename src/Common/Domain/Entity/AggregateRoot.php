<?php

declare(strict_types=1);

namespace MarketPlace\Common\Domain\Entity;

interface AggregateRoot
{
    public function releaseEvents(): array;
}
