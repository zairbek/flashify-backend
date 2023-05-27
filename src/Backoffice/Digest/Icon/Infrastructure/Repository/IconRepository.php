<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Digest\Icon\Infrastructure\Repository;

use App\Models\Icon as IconModel;
use Illuminate\Http\UploadedFile;
use MarketPlace\Backoffice\Digest\Icon\Application\Dto\GetIconDto;
use MarketPlace\Backoffice\Digest\Icon\Domain\Entity\Icon;
use MarketPlace\Backoffice\Digest\Icon\Domain\Repository\IconRepositoryInterface;
use MarketPlace\Backoffice\Digest\Icon\Domain\ValueObject\IconName;
use MarketPlace\Backoffice\Digest\Icon\Infrastructure\Exception\IconAlreadyExistsException;
use MarketPlace\Backoffice\Digest\Icon\Infrastructure\Exception\IconNotFoundException;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Common\Infrastructure\Service\Collection;
use MarketPlace\Common\Infrastructure\Service\Hydrator;

class IconRepository implements IconRepositoryInterface
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

    public function get(GetIconDto $dto): Collection
    {
        $query = IconModel::query()->with('media');
        $count = $query->count();

        if ($dto->search) {
            $query->where('name', 'like', "%$dto->search%");
        }

        $query->orderBy($dto->sortField, $dto->sortDirection);
        $query->offset($dto->offset ?? 0);
        $query->limit($dto->limit ?? 10);

        $result = $query->get();

        return (new Collection($result))->map(function (IconModel $icon) {
            return $this->iconHydrator($icon);
        })
            ->setTotal($count)
            ->setLimit($dto->limit)
            ->setOffset($dto->offset)
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

    private function iconHydrator(IconModel $iconModel): Icon
    {
        return $this->hydrator->hydrate(Icon::class, [
            'uuid' => new Uuid($iconModel->uuid),
            'name' => new IconName($iconModel->name),
            'file' => $iconModel->toIconFile()
        ]);
    }
}
