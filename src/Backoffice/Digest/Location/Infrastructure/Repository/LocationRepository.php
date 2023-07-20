<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Digest\Location\Infrastructure\Repository;

use App\Models\Icon as IconModel;
use App\Models\Location as LocationModel;
use Illuminate\Http\UploadedFile;
use MarketPlace\Backoffice\Digest\Icon\Application\Dto\GetIconDto;
use MarketPlace\Backoffice\Digest\Icon\Domain\Entity\Icon;
use MarketPlace\Backoffice\Digest\Icon\Domain\Repository\IconRepositoryInterface;
use MarketPlace\Backoffice\Digest\Icon\Domain\ValueObject\IconName;
use MarketPlace\Backoffice\Digest\Icon\Infrastructure\Exception\IconAlreadyExistsException;
use MarketPlace\Backoffice\Digest\Icon\Infrastructure\Exception\IconNotFoundException;
use MarketPlace\Backoffice\Digest\Location\Application\Dto\GetLocationDto;
use MarketPlace\Backoffice\Digest\Location\Domain\Entity\Location;
use MarketPlace\Backoffice\Digest\Location\Domain\Repository\LocationRepositoryInterface;
use MarketPlace\Backoffice\Digest\Location\Domain\ValueObject\LocationName;
use MarketPlace\Common\Domain\Criteria\CriteriaInterface;
use MarketPlace\Common\Domain\ValueObject\ExternalId;
use MarketPlace\Common\Domain\ValueObject\Id;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Common\Infrastructure\Service\Collection;
use MarketPlace\Common\Infrastructure\Service\Hydrator;

class LocationRepository implements LocationRepositoryInterface
{
    private Hydrator $hydrator;

    public function __construct()
    {
        $this->hydrator = new Hydrator();
    }

    /**
     * @throws IconAlreadyExistsException
     */
    public function create(Icon $icon): void
    {
        if ($this->existsByName($icon->getName())) {
            throw new IconAlreadyExistsException();
        }

        /** @var IconModel $iconModel */
        $iconModel = IconModel::create([
            'uuid' => $icon->getUuid()->getId(),
            'name' => $icon->getName()->getName()
        ]);

        $iconModel->addIcon(new UploadedFile(
            path: $icon->getFile()->getFilePath(),
            originalName: $icon->getFile()->getOriginalName(),
            mimeType: $icon->getFile()->getMimeType()
        ));
    }

    /**
     * @throws IconAlreadyExistsException
     * @throws IconNotFoundException
     */
    public function update(Icon $icon): void
    {
        if ($this->existsByNameWithout($icon->getName(), $icon->getUuid())) {
            throw new IconAlreadyExistsException();
        }

        /** @var IconModel $iconModel */
        $iconModel = IconModel::query()->where('uuid', $icon->getUuid()->getId())->first();

        if (is_null($iconModel)) {
            throw new IconNotFoundException();
        }

        $iconModel->update([
            'name' => $icon->getName()->getName()
        ]);

        if (! $icon->getFile()->isUploaded()) {
             $iconModel->addIcon(new UploadedFile(
                 path: $icon->getFile()->getFilePath(),
                 originalName: $icon->getFile()->getOriginalName(),
                 mimeType: $icon->getFile()->getMimeType()
             ));
        }
    }

    public function delete(Icon $icon): void
    {
        IconModel::where('uuid', $icon->getUuid()->getId())->first()->delete();
    }

    /**
     * @throws IconNotFoundException
     */
    public function find(Uuid $uuid): Icon
    {
        /** @var IconModel $iconModel */
        $iconModel = IconModel::query()->with('media')->where('uuid', $uuid->getId())->first();

        if (is_null($iconModel)) {
            throw new IconNotFoundException();
        }

        return $this->iconHydrator($iconModel);
    }

    public function get(GetLocationDto $dto): Collection
    {
        $query = LocationModel::query();

        if ($dto->parentId) {
            $query->where('parent_id', $dto->parentId);
        } else {
            $query->whereNull('parent_id');
        }

        $count = $query->count();

        if ($dto->search) {
            $query->where('name', 'like', "%$dto->search%");
        }

        $query->orderBy($dto->sortField, $dto->sortDirection);
        $query->offset($dto->offset ?? 0);
        $query->limit($dto->limit ?? 10);

        $result = $query->get();

        return (new Collection($result))->map(function (LocationModel $location) {
            return $this->locationHydrator($location);
        })
            ->setTotal($count)
            ->setLimit($dto->limit)
            ->setOffset($dto->offset)
            ->setAdditional([
                'parent' => $result->first()?->parent?->parent?->id,
                'current' => $dto->parentId
            ])
            ;
    }

    private function existsByName(IconName $name): bool
    {
        return IconModel::query()->where('name', $name->getName())->exists();
    }

    private function existsByNameWithout(IconName $name, Uuid $uuid): bool
    {
        return IconModel::query()->whereNot('uuid', $uuid->getId())
            ->where('name', $name->getName())->exists();
    }

    /**
     * @throws IconNotFoundException
     */
    public function findByName(IconName $name): Icon
    {
        /** @var IconModel|null $iconModel */
        $iconModel = IconModel::query()->where('name', $name->getName())->first();

        if (is_null($iconModel)) {
            throw new IconNotFoundException();
        }

        return $this->iconHydrator($iconModel);
    }

    public function filter(CriteriaInterface $criteria): Collection
    {
        $iconModel = IconModel::query()
            ->whereIn($criteria->getColumn(), $criteria->getValue())
            ->get()
        ;

        return (new Collection($iconModel))->map(fn ($icon) => $this->iconHydrator($icon));
    }

    private function locationHydrator(LocationModel $locationModel): Location
    {
        return $this->hydrator->hydrate(Location::class, [
            'id' => new Id($locationModel->id),
            'parentId' => $locationModel->parent_id ? new Id($locationModel->parent_id) : null,
            'externalId' => $locationModel->externalId ? new ExternalId($locationModel->externalId) : null,
            'name' => LocationName::fromDB($locationModel->name, $locationModel->translates),
        ]);
    }
}
