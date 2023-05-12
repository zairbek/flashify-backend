<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Backoffice\V1\Categories;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MarketPlace\Backoffice\Category\Application\Service\CategoryService;

class ListCategoriesController extends Controller
{
    private CategoryService $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    public function __invoke(Request $request): JsonResponse
    {
        dd($this->service);

        return response()->json([]);
    }
}
