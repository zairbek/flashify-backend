<?php

namespace MarketPlace\Common\Domain\Entity;

use MarketPlace\Common\Domain\Events\EventInterface;

trait EventTrait
{
    /**
     * @var array<EventInterface>
     */
    private array $events = [];

    protected function recordEvent(EventInterface $event): void
    {
        $this->events[] = $event;
    }

    public function releaseEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }
}
