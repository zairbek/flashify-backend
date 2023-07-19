<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Market\V1\Categories;

use App\Http\Controllers\Controller;
use App\Http\Requests\Market\Categories\GetCategoryRequest;
use Illuminate\Http\JsonResponse;
use MarketPlace\Market\Category\Application\Dto\GetCategoryDto;
use MarketPlace\Market\Category\Application\Service\CategoryService;

class ListCategoriesController extends Controller
{
    private CategoryService $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    public function __invoke(GetCategoryRequest $request): JsonResponse
    {
        $limit = $request->get('limit');
        $offset = $request->get('offset');

        $categories = $this->service->getCategories(new GetCategoryDto(
            search: $request->get('name'),
            limit: $limit ? (int) $limit : null,
            offset: $offset ? (int) $offset : null,
            sortField: $request->get('sortField'),
            sortDirection: $request->get('sortDirection'),
            parentUuid: $request->get('parentUuid')
        ));

        return response()->json([
            'data' => $categories,
            'meta' => $categories->getMetaData()
        ]);
    }
}
