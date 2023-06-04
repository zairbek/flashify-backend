<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Digest\Icon\Infrastructure\Criteria;

use MarketPlace\Common\Domain\Criteria\CriteriaInterface;

class ByUuidCriteria implements CriteriaInterface
{
    private string|array $value;

    public function __construct(array|string $value)
    {
        $this->value = $value;
    }

    public function getColumn(): string
    {
        return 'uuid';
    }

    public function getOperator(): string
    {
        return '=';
    }

    public function getValue(): array|string
    {
        return $this->value;
    }
}
