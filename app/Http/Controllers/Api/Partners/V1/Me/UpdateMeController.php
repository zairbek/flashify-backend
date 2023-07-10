<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Partners\V1\Me;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partners\Me\UpdateMeRequest;
use Auth;
use Illuminate\Http\JsonResponse;
use MarketPlace\Partners\User\Application\Dto\UpdateUserNameDto;
use MarketPlace\Partners\User\Application\Service\UserService;
use MarketPlace\Partners\User\Infrastructure\Exception\UserNotFoundException;

class UpdateMeController extends Controller
{
    private UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function __invoke(UpdateMeRequest $request): JsonResponse
    {
        try {
            $this->service->updateUserName(new UpdateUserNameDto(
                Auth::user()?->getKey(),
                $request->get('firstName'),
                $request->get('lastName'),
            ));
            return response()->json(['message' => 'ok']);
        } catch (UserNotFoundException $e) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
    }
}
