<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Digest\Location\Application\Service;

use MarketPlace\Backoffice\Digest\Icon\Application\Dto\CreateIconDto;
use MarketPlace\Backoffice\Digest\Icon\Application\Dto\GetIconDto;
use MarketPlace\Backoffice\Digest\Icon\Application\Dto\UpdateIconDto;
use MarketPlace\Backoffice\Digest\Icon\Domain\Entity\Icon;
use MarketPlace\Backoffice\Digest\Icon\Domain\Repository\IconRepositoryInterface;
use MarketPlace\Backoffice\Digest\Icon\Domain\ValueObject\IconName;
use MarketPlace\Backoffice\Digest\Icon\Infrastructure\Criteria\ByUuidCriteria;
use MarketPlace\Backoffice\Digest\Icon\Infrastructure\Exception\IconAlreadyExistsException;
use MarketPlace\Backoffice\Digest\Icon\Infrastructure\Exception\IconNotFoundException;
use MarketPlace\Backoffice\Digest\Location\Application\Dto\GetLocationDto;
use MarketPlace\Backoffice\Digest\Location\Domain\Repository\LocationRepositoryInterface;
use MarketPlace\Common\Domain\Entity\AggregateRoot;
use MarketPlace\Common\Domain\Entity\EventTrait;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Common\Infrastructure\Service\Collection;

class LocationService implements AggregateRoot
{
    use EventTrait;

    private LocationRepositoryInterface $repository;

    public function __construct(LocationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getLocations(GetLocationDto $dto): Collection
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

    /**
     * @throws IconNotFoundException
     */
    public function deleteIcon(Uuid $uuid): void
    {
        $category = $this->repository->find($uuid);

        $this->repository->delete($category);
    }

    /**
     * @throws IconNotFoundException
     */
    public function showIcon(string $uuid): Icon
    {
        return $this->repository->find(new Uuid($uuid));
    }

    /**
     * @throws IconNotFoundException
     * @throws IconAlreadyExistsException
     */
    public function updateIcon(UpdateIconDto $dto): void
    {
        $icon = $this->repository->find(new Uuid($dto->uuid));

        if ($dto->name) {
            $icon->changeName(new IconName($dto->name));
        }

        if ($dto->file) {
            $icon->changeFile($dto->file);
        }

        $this->repository->update($icon);
    }

    public function getByUuids(array $uuids): array
    {
        $icons = $this->repository->filter(new ByUuidCriteria($uuids));

        return $icons->map(function (Icon $icon) {
            return [
                'uuid' => $icon->getUuid()->getId(),
                'name' => $icon->getName()->getName(),
                'file' => $icon->getFile()->getFilePath()
            ];
        })->toArray();
    }
}
