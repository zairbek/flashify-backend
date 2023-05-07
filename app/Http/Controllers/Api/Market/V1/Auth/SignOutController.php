<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Market\V1\Auth;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;
use MarketPlace\Market\Auth\Application\Dto\SignOutDto;
use MarketPlace\Market\Auth\Application\Service\AuthorizeService;

class SignOutController extends Controller
{
    private AuthorizeService $service;

    public function __construct(AuthorizeService $service)
    {
        $this->service = $service;
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $token = $request->bearerToken();
            if (is_null($token)) {
                throw new UnauthorizedException();
            }
            $this->service->signOut(new SignOutDto(bearerToken: $token));

            return response()->json([], Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
