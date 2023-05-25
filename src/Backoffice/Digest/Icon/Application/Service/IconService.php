<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Digest\Icon\Application\Service;

use MarketPlace\Backoffice\Digest\Icon\Application\Dto\CreateIconDto;
use MarketPlace\Backoffice\Digest\Icon\Application\Dto\GetIconDto;
use MarketPlace\Backoffice\Digest\Icon\Domain\Entity\Icon;
use MarketPlace\Backoffice\Digest\Icon\Domain\Repository\IconRepositoryInterface;
use MarketPlace\Backoffice\Digest\Icon\Domain\ValueObject\IconName;
use MarketPlace\Backoffice\Digest\Icon\Infrastructure\Exception\IconAlreadyExistsException;
use MarketPlace\Common\Domain\Entity\AggregateRoot;
use MarketPlace\Common\Domain\Entity\EventTrait;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Common\Infrastructure\Service\Collection;

class IconService implements AggregateRoot
{
    use EventTrait;

    private IconRepositoryInterface $repository;

    public function __construct(IconRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getIcons(GetIconDto $dto): Collection
    {
        return $this->repository->get($dto);
    }

    /**
     * @throws IconAlreadyExistsException
     */
    public function createIcon(CreateIconDto $dto): void
    {
        $icon = new Icon(
            uuid: Uuid::next(),
            name: new IconName($dto->name),
            file: $dto->file,
        );

        $this->repository->create($icon);
    }
}
