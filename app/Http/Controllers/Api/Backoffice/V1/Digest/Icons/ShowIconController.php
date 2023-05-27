<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Backoffice\V1\Digest\Icons;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MarketPlace\Backoffice\Digest\Icon\Application\Service\IconService;
use MarketPlace\Backoffice\Digest\Icon\Infrastructure\Exception\IconNotFoundException;

class ShowIconController extends Controller
{
    private IconService $service;

    public function __construct(IconService $service)
    {
        $this->service = $service;
    }

    public function __invoke(Request $request, $uuid): JsonResponse
    {
        try {
            $icon = $this->service->showIcon($uuid);
            return response()->json($icon->toArray());
        } catch (IconNotFoundException $e) {
            return response()->json(['message' => 'not found'], 404);
        }
    }
}
