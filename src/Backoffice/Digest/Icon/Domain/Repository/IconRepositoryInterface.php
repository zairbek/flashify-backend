<?php

namespace MarketPlace\Backoffice\Digest\Icon\Domain\Repository;

use MarketPlace\Backoffice\Digest\Icon\Application\Dto\GetIconDto;
use MarketPlace\Backoffice\Digest\Icon\Domain\Entity\Icon;
use MarketPlace\Backoffice\Digest\Icon\Infrastructure\Exception\IconAlreadyExistsException;
use MarketPlace\Common\Infrastructure\Service\Collection;

interface IconRepositoryInterface
{

    public function get(GetIconDto $dto): Collection;


    /**
     * @throws IconAlreadyExistsException
     */
    public function create(Icon $icon): void;
}
