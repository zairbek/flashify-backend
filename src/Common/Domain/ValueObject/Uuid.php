<?php

declare(strict_types=1);

namespace MarketPlace\Common\Domain\ValueObject;

use Ramsey\Uuid\Uuid as RamseyUuid;
use Webmozart\Assert\Assert;

class Uuid
{
    private string $id;

    public function __construct(string $id)
    {
        Assert::notEmpty($id);

        $this->id = $id;
    }

    public static function next(): self
    {
        return new self(RamseyUuid::uuid4()->toString());
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function isEqualTo(self $other): bool
    {
        return $this->getId() === $other->getId();
    }
}
