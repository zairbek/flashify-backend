<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Digest\Icon\Application\Service;

use MarketPlace\Backoffice\Digest\Icon\Application\Dto\GetIconDto;
use MarketPlace\Backoffice\Digest\Icon\Domain\Repository\IconRepositoryInterface;
use MarketPlace\Common\Infrastructure\Service\Collection;

class IconService
{
    private IconRepositoryInterface $repository;

    public function __construct(IconRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getIcons(GetIconDto $dto): Collection
    {
        return $this->repository->get($dto);
    }
}
