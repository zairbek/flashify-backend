<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Category\Infrastructure\Adapter;

use Illuminate\Support\Collection;
use MarketPlace\Backoffice\Category\Domain\Entity\CategoryIcon;
use MarketPlace\Backoffice\Category\Domain\ValueObject\Name;
use MarketPlace\Backoffice\Category\Infrastructure\Exception\CategoryIconNotFoundException;
use MarketPlace\Backoffice\Digest\Icon\Infrastructure\Api\IconApi;
use MarketPlace\Common\Domain\ValueObject\Url;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Common\Infrastructure\Service\Hydrator;

class IconAdapter
{
    private IconApi $api;
    private Hydrator $hydrator;

    public function __construct()
    {
        $this->api = new IconApi();
        $this->hydrator = new Hydrator();
    }

    /**
     * @throws CategoryIconNotFoundException
     */
    public function findByUuid(Uuid $uuid): CategoryIcon
    {
        $icon = $this->api->findByUuid($uuid->getId());

        if (is_null($icon)) {
            throw new CategoryIconNotFoundException();
        }

        return $this->categoryIconHydrator($icon);
    }

    public function getByUuids(Collection $iconUuids): Collection
    {
        $iconUuidArray = $iconUuids->map(fn (Uuid $uuid) => $uuid->getId())->toArray();

        return collect($this->api->getByUuids($iconUuidArray))
            ->map(fn (array $icon) => $this->categoryIconHydrator($icon));
    }

    private function categoryIconHydrator(array $icon): CategoryIcon
    {
        return $this->hydrator->hydrate(CategoryIcon::class, [
            'uuid' => new Uuid($icon['uuid']),
            'name' => new Name($icon['name']),
            'url' => new Url($icon['file'])
        ]);
    }
}
