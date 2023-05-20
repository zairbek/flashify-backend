<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Backoffice\V1\Digest\Icons;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MarketPlace\Backoffice\Digest\Icon\Application\Dto\GetIconDto;
use MarketPlace\Backoffice\Digest\Icon\Application\Service\IconService;
use MarketPlace\Backoffice\Digest\Icon\Domain\Entity\Icon;

class ListIconsController extends Controller
{
    private IconService $service;

    public function __construct(IconService $service)
    {
        $this->service = $service;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $limit = $request->get('limit');
        $offset = $request->get('offset');

        $icons = $this->service->getIcons(new GetIconDto(
            search: $request->get('name'),
            limit: $limit ? (int) $limit : null,
            offset: $offset ? (int) $offset : null,
            sortField: $request->get('sortField'),
            sortDirection: $request->get('sortDirection'),
        ));

        return response()->json([
            'data' => $icons->map(fn (Icon $icon) => $icon->toArray()),
            'meta' => $icons->getMetaData()
        ]);
    }
}
