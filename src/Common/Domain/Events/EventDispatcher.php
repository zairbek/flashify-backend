<?php

declare(strict_types=1);

namespace MarketPlace\Common\Domain\Events;

class EventDispatcher
{

    /**
     * @var array<string, array<string>>
     */
    private array $events;

    public function __construct(array $events)
    {
        $this->events = $events;
    }

    /**
     * @param array<EventInterface> $releaseEvents
     * @return void
     */
    public function dispatch(array $releaseEvents): void
    {
        foreach ($releaseEvents as $releaseEvent) {
            $eventClass = get_class($releaseEvent);

            if (array_key_exists($eventClass, $this->events)) {
                foreach ($this->events[$eventClass] as $event) {
                    (new $event)($releaseEvent);
                }
            }
        }
    }
}
