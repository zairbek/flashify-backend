<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Market\V1\Categories;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Market\Category\Application\Service\CategoryService;
use MarketPlace\Market\Category\Infrastructure\Exception\CategoryNotFoundException;

class DeleteCategoryController extends Controller
{
    private CategoryService $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    public function __invoke(Request $request, $uuid): JsonResponse
    {
        try {
            $this->service->deleteCategory(new Uuid($uuid));
            return response()->json(['message' => 'ok'], 204);
        } catch (CategoryNotFoundException $e) {
            return response()->json(['message' => 'not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
