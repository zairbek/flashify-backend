<?php

namespace MarketPlace\Common\Domain\Criteria;

interface CriteriaInterface
{
    public function getColumn(): string;

    public function getOperator(): string;
    public function getValue(): array|string;
}
