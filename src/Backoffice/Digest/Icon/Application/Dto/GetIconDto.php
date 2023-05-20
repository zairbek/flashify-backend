<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Digest\Icon\Application\Dto;

class GetIconDto
{
    public ?string $search;
    public ?int $limit;
    public ?int $offset;
    public ?string $sortField;
    public ?string $sortDirection;

    public function __construct(
        ?string $search = null,
        ?int $limit = null,
        ?int $offset = null,
        ?string $sortField = null,
        ?string $sortDirection = null,
    )
    {
        $this->search = $search;
        $this->limit = $limit ?? 10;
        $this->offset = $offset ?? 0;
        $this->sortField = $sortField ?? 'created_at';
        $this->sortDirection = $sortDirection ?? 'asc';
    }
}
