<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Digest\Icon\Infrastructure\Api;

use MarketPlace\Backoffice\Digest\Icon\Application\Service\IconService;
use MarketPlace\Backoffice\Digest\Icon\Infrastructure\Exception\IconNotFoundException;

class IconApi
{
    private IconService $service;

    public function __construct()
    {
        $this->service = \App::make(IconService::class);
    }

    public function findByUuid(string $uuid): ?array
    {
        try {
            return $this->service->showIcon($uuid)->toArray();
        } catch (IconNotFoundException $e) {
            return null;
        }
    }

    public function getByUuids(array $iconUuidArray): array
    {
        return $this->service->getByUuids($iconUuidArray);
    }
}
