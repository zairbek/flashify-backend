<?php

declare(strict_types=1);

namespace MarketPlace\Common\Domain\ValueObject\Abstracts;

use DateTime;
use DateTimeZone;
use Exception;

abstract class DateTimeAbstract
{
    final public function __construct(
        protected DateTime $dateTime
    )
    {
    }

    /**
     * @return DateTime
     */
    public function getDateTime(): DateTime
    {
        return $this->dateTime;
    }

    /**
     * @throws Exception
     */
    public static function now(): static
    {
        return new static(new DateTime(timezone: new DateTimeZone('UTC')));
    }

    public function toIsoFormat(): string
    {
        return $this->getDateTime()->format('c');
    }

    /**
     * @throws Exception
     */
    public static function fromIsoFormat(string $isoString): static
    {
        return new static(new DateTime($isoString));
    }
}
