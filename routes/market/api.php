<?php

use App\Http\Controllers\Api\Market\V1\Auth\Phone\RequestCodeToPhoneNumberController;
use App\Http\Controllers\Api\Market\V1\Auth\Phone\SignInWithPhoneAndCodeController;
use App\Http\Controllers\Api\Market\V1\DocumentationController;
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


Route::get('healthCheck', function () {
    return Response::json(['message' => 'ok']);
});

Route::prefix('auth')->group(function () {
    Route::prefix('phone')->group(function () {
        Route::post('request', RequestCodeToPhoneNumberController::class);
        Route::post('sign-in', SignInWithPhoneAndCodeController::class);
    });
});
