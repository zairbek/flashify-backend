<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Backoffice\V1\Categories;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListCategoriesController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json([]);
    }
}
