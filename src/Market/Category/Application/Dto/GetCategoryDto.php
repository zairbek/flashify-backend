<?php

declare(strict_types=1);

namespace MarketPlace\Market\Category\Application\Dto;

class GetCategoryDto
{
    public ?string $search;
    public ?int $limit;
    public ?int $offset;
    public ?string $sortField;
    public ?string $sortDirection;
    public ?string $parentUuid;

    public function __construct(
        ?string $search = null,
        ?int $limit = null,
        ?int $offset = null,
        ?string $sortField = null,
        ?string $sortDirection = null,
        ?string $parentUuid = null,
    )
    {
        $this->search = $search;
        $this->limit = $limit ?? 10;
        $this->offset = $offset ?? 0;
        $this->sortField = $sortField ?? 'created_at';
        $this->sortDirection = $sortDirection ?? 'asc';
        $this->parentUuid = $parentUuid;
    }
}
