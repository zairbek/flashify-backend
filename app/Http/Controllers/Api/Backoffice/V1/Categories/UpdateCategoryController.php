<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Backoffice\V1\Categories;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\Categories\UpdateCategoryRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use MarketPlace\Backoffice\Category\Application\Dto\UpdateCategoryDto;
use MarketPlace\Backoffice\Category\Application\Service\CategoryService;
use MarketPlace\Backoffice\Category\Infrastructure\Exception\CategoryIconNotFoundException;
use MarketPlace\Backoffice\Category\Infrastructure\Exception\CategoryNotFoundException;
use MarketPlace\Backoffice\Category\Infrastructure\Exception\CategorySlugAlreadyExistsException;

class UpdateCategoryController extends Controller
{
    private CategoryService $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    public function __invoke(UpdateCategoryRequest $request, $uuid): JsonResponse
    {
        try {
            $this->service->updateCategory(new UpdateCategoryDto(
                uuid: $uuid,
                name: $request->get('name'),
                slug: $request->get('slug'),
                description: $request->get('description'),
                parentCategory: $request->get('parentCategory'),
                active: $request->get('active'),
                iconUuid: $request->get('icon_uuid'),
            ));
            return response()->json(['message' => 'ok']);
        } catch (CategoryNotFoundException $e) {
            return response()->json(['message' => 'not found'], 404);
        } catch (CategorySlugAlreadyExistsException $e) {
            throw ValidationException::withMessages(['slug' => ['Slug с таким именем уже существует в базе']]);
        } catch (CategoryIconNotFoundException $e) {
            throw ValidationException::withMessages(['icon_uuid' => ['Такой иконки не существует']]);
        }
    }
}
