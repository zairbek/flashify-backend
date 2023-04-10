<?php

declare(strict_types=1);

namespace MarketPlace\Common\Domain\ValueObject;

use Webmozart\Assert\Assert;

class UserStatus
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';
    const BAN = 'ban';
    private string $status;

    public function __construct(string $status)
    {
        Assert::inArray($status, [self::ACTIVE, self::INACTIVE, self::BAN]);

        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    public static function createActiveStatus(): self
    {
        return new self(self::ACTIVE);
    }

    public function isActive(): bool
    {
        return $this->status === self::ACTIVE;
    }

    public function isInactive(): bool
    {
        return $this->status === self::INACTIVE;
    }

    public function isBanned(): bool
    {
        return $this->status === self::BAN;
    }
}
