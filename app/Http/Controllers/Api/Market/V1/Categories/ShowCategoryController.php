<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Market\V1\Categories;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MarketPlace\Market\Category\Application\Service\CategoryService;
use MarketPlace\Market\Category\Infrastructure\Exception\CategoryNotFoundException;

class ShowCategoryController extends Controller
{
    private CategoryService $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    public function __invoke(Request $request, $uuid): JsonResponse
    {
        try {
            $category = $this->service->showCategory($uuid);
            return response()->json($category->toArray());
        } catch (CategoryNotFoundException $e) {
            return response()->json(['message' => 'not found'], 404);
        }
    }
}
