<?php

namespace MarketPlace\Backoffice\Digest\Icon\Domain\Repository;

use MarketPlace\Backoffice\Digest\Icon\Application\Dto\GetIconDto;
use MarketPlace\Backoffice\Digest\Icon\Domain\Entity\Icon;
use MarketPlace\Backoffice\Digest\Icon\Infrastructure\Exception\IconAlreadyExistsException;
use MarketPlace\Backoffice\Digest\Icon\Infrastructure\Exception\IconNotFoundException;
use MarketPlace\Common\Domain\Criteria\CriteriaInterface;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Common\Infrastructure\Service\Collection;

interface IconRepositoryInterface
{

    public function get(GetIconDto $dto): Collection;


    /**
     * @throws IconAlreadyExistsException
     */
    public function create(Icon $icon): void;

    public function delete(Icon $icon): void;

    /**
     * @param Uuid $uuid
     * @return Icon
     * @throws IconNotFoundException
     */
    public function find(Uuid $uuid): Icon;

    /**
     * @param Icon $icon
     * @return void
     * @throws IconAlreadyExistsException
     * @throws IconNotFoundException
     */
    public function update(Icon $icon): void;

    public function filter(CriteriaInterface $criteria): Collection;
}
