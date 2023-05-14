<?php

declare(strict_types=1);

namespace MarketPlace\Common\Infrastructure\Service;

use Illuminate\Support\Collection as LaraCollection;

class Collection extends LaraCollection
{
    private array $metaData = [
        'total' => 0,
        'offset' => 0,
        'limit' => 0,
        'additional' => null
    ];

    public function setTotal(int $total): self
    {
        $this->metaData['total'] = $total;
        return $this;
    }

    public function setOffset(int $offset): self
    {
        $this->metaData['offset'] = $offset;
        return $this;
    }

    public function setLimit(int $limit): self
    {
        $this->metaData['limit'] = $limit;
        return $this;
    }

    public function setAdditional(?array $additional): self
    {
        $this->metaData['additional'] = $additional;
        return $this;
    }

    public function getMetaData(): array
    {
        return $this->metaData;
    }
}
