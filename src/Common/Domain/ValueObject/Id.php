<?php

declare(strict_types=1);

namespace MarketPlace\Common\Domain\ValueObject;

use Webmozart\Assert\Assert;

class Id
{
    private int $id;

    public function __construct(int $id)
    {
        Assert::notEmpty($id);

        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function isEqualTo(self $other): bool
    {
        return $this->getId() === $other->getId();
    }
}
