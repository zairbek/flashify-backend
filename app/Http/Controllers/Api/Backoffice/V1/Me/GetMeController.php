<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Backoffice\V1\Me;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MarketPlace\Market\User\Application\Service\UserService;
use MarketPlace\Market\User\Infrastructure\Exception\UserUnauthenticatedException;

class GetMeController extends Controller
{
    private UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $user = $this->service->me();

            return response()->json($user->toArray());
        } catch (UserUnauthenticatedException $e) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
