<?php

declare(strict_types=1);

namespace MarketPlace\Common\Domain\ValueObject;

class ActiveStatus
{
    private bool $status;

    public function __construct(bool $status)
    {
        $this->status = $status;
    }

    public static function inactive(): self
    {
        return new self(false);
    }

    public static function active(): self
    {
        return new self(true);
    }

    /**
     * @return bool
     */
    public function isStatus(): bool
    {
        return $this->status;
    }
}
