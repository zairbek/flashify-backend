<?php

declare(strict_types=1);

namespace MarketPlace\Partners\Account\Domain\ValueObject;

use Webmozart\Assert\Assert;

class AccountStatus
{
    private const ACTIVE = 'active';
    private const INACTIVE = 'inactive';
    private const BAN = 'ban';
    private string $status;

    public function __construct(string $status)
    {
        Assert::inArray($status, [self::ACTIVE, self::INACTIVE, self::BAN]);

        $this->status = $status;
    }

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
        return $this->getStatus() === self::ACTIVE;
    }

    public function isInactive(): bool
    {
        return $this->getStatus() === self::INACTIVE;
    }

    public function isBanned(): bool
    {
        return $this->getStatus() === self::BAN;
    }
}
