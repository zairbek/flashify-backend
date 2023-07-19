<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Market\V1\Categories;

use App\Http\Controllers\Controller;
use App\Http\Requests\Market\Categories\CreateCategoryRequest;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use MarketPlace\Market\Category\Application\Dto\CreateCategoryDto;
use MarketPlace\Market\Category\Application\Service\CategoryService;
use MarketPlace\Market\Category\Infrastructure\Exception\CategoryIconNotFoundException;
use MarketPlace\Market\Category\Infrastructure\Exception\CategoryNotFoundException;
use MarketPlace\Market\Category\Infrastructure\Exception\CategorySlugAlreadyExistsException;

class CreateCategoryController extends Controller
{
    private CategoryService $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    public function __invoke(CreateCategoryRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->service->createCategory(new CreateCategoryDto(
                name: $request->get('name'),
                slug: $request->get('slug'),
                description: $request->get('description'),
                parentCategory: $request->get('parentCategory'),
                active: $request->get('active'),
                icon: $request->get('icon'),
            ));
            DB::commit();
            return response()->json(['message' => 'created'], 201);
        } catch (CategoryNotFoundException $e) {
            DB::rollBack();
            throw ValidationException::withMessages(['parentCategory' => ['Родительская категория не существует']]);
        } catch (CategorySlugAlreadyExistsException $e) {
            DB::rollBack();
            throw ValidationException::withMessages(['slug' => ['Slug с таким именем уже существует в базе']]);
        } catch (CategoryIconNotFoundException $e) {
            DB::rollBack();
            throw ValidationException::withMessages(['icon' => ['Такой иконки не существует']]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
