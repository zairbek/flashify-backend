<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Backoffice\V1\Digest\Locations;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MarketPlace\Backoffice\Digest\Location\Application\Dto\GetLocationDto;
use MarketPlace\Backoffice\Digest\Location\Application\Service\LocationService;
use MarketPlace\Backoffice\Digest\Location\Domain\Entity\Location;

class ListLocationsController extends Controller
{
    private LocationService $service;

    public function __construct(LocationService $service)
    {
        $this->service = $service;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $limit = $request->get('limit');
        $offset = $request->get('offset');

        $icons = $this->service->getLocations(new GetLocationDto(
            search: $request->get('name'),
            limit: $limit ? (int) $limit : null,
            offset: $offset ? (int) $offset : null,
            sortField: $request->get('sortField'),
            sortDirection: $request->get('sortDirection'),
            parentId: $request->get('parentId'),
        ));

        return response()->json([
            'data' => $icons->map(fn (Location $location) => $location->toArray()),
            'meta' => $icons->getMetaData()
        ]);
    }
}
