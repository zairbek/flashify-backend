<?php

namespace MarketPlace\Backoffice\Digest\Icon\Domain\Repository;

use MarketPlace\Backoffice\Digest\Icon\Application\Dto\GetIconDto;
use MarketPlace\Common\Infrastructure\Service\Collection;

interface IconRepositoryInterface
{

    public function get(GetIconDto $dto): Collection;
}
