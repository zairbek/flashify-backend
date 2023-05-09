<?php

use App\Http\Controllers\Api\Backoffice\V1\Auth\SignInController;
use App\Http\Controllers\Api\Backoffice\V1\Auth\RefreshTokenController;
use App\Http\Controllers\Api\Backoffice\V1\Auth\SignOutController;
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
    Route::get('sign-out', SignOutController::class)->middleware(['auth:api']);
});


Route::middleware(['auth:api'])->group(function () {
    Route::get('me', GetMeController::class);
});
