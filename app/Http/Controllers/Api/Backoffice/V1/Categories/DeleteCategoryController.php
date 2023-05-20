<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Backoffice\V1\Categories;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MarketPlace\Backoffice\Category\Application\Service\CategoryService;
use MarketPlace\Backoffice\Category\Infrastructure\Exception\CategoryNotFoundException;
use MarketPlace\Common\Domain\ValueObject\Uuid;

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
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
