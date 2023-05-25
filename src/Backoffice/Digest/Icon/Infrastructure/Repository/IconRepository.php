<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Digest\Icon\Infrastructure\Repository;

use App\Models\Icon as IconModel;
use Illuminate\Http\UploadedFile;
use MarketPlace\Backoffice\Digest\Icon\Application\Dto\GetIconDto;
use MarketPlace\Backoffice\Digest\Icon\Domain\Entity\Icon;
use MarketPlace\Backoffice\Digest\Icon\Domain\Repository\IconRepositoryInterface;
use MarketPlace\Backoffice\Digest\Icon\Domain\ValueObject\IconFile;
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
