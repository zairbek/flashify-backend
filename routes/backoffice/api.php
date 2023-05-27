<?php

use App\Http\Controllers\Api\Backoffice\V1\Auth\SignInController;
use App\Http\Controllers\Api\Backoffice\V1\Auth\RefreshTokenController;
use App\Http\Controllers\Api\Backoffice\V1\Auth\SignOutController;
use App\Http\Controllers\Api\Backoffice\V1\Categories\CreateCategoryController;
use App\Http\Controllers\Api\Backoffice\V1\Categories\DeleteCategoryController;
use App\Http\Controllers\Api\Backoffice\V1\Categories\ListCategoriesController;
use App\Http\Controllers\Api\Backoffice\V1\Categories\ShowCategoryController;
use App\Http\Controllers\Api\Backoffice\V1\Categories\UpdateCategoryController;
use App\Http\Controllers\Api\Backoffice\V1\Digest\Icons\CreateIconController;
use App\Http\Controllers\Api\Backoffice\V1\Digest\Icons\DeleteIconController;
use App\Http\Controllers\Api\Backoffice\V1\Digest\Icons\ListIconsController;
use App\Http\Controllers\Api\Backoffice\V1\DocumentationController;
use App\Http\Controllers\Api\Backoffice\V1\Me\GetMeController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('documentation')->group(function () {
    Route::get('/', [DocumentationController::class, 'ui']);
    Route::get('/file', [DocumentationController::class, 'file']);
});


Route::get('healthCheck', static function () {
    return Response::json(['message' => 'ok']);
});

Route::prefix('auth')->group(function () {
    Route::post('sign-in', SignInController::class);
    Route::post('refresh-token', RefreshTokenController::class);
    Route::get('sign-out', SignOutController::class)->middleware(['auth:api-backoffice']);
});

Route::middleware(['auth:api-backoffice'])->group(function () {
    Route::get('me', GetMeController::class);

    Route::prefix('categories')->group(function () {
        Route::get('', ListCategoriesController::class);
        Route::post('', CreateCategoryController::class);
        Route::get('{uuid}', ShowCategoryController::class);
        Route::put('{uuid}', UpdateCategoryController::class);
        Route::delete('{uuid}', DeleteCategoryController::class);
    });

    Route::prefix('digest')->group(function () {
        Route::prefix('icons')->group(function () {
            Route::get('', ListIconsController::class);
            Route::post('', CreateIconController::class);
//            Route::get('{uuid}', DeleteIconController::class);
//            Route::put('{uuid}', DeleteIconController::class);
            Route::delete('{uuid}', DeleteIconController::class);
        });
    });

});

