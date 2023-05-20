<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Backoffice\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\Auth\RefreshingTokenRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use MarketPlace\Backoffice\Auth\Application\Dto\RefreshingTokenDto;
use MarketPlace\Backoffice\Auth\Application\Service\AuthorizeService;
use Symfony\Component\HttpFoundation\Cookie;

class RefreshTokenController extends Controller
{
    private AuthorizeService $service;

    public function __construct(AuthorizeService $service)
    {
        $this->service = $service;
    }

    public function __invoke(RefreshingTokenRequest $request): JsonResponse
    {
        try {
            $token = $this->service->refreshingToken(new RefreshingTokenDto(refreshToken: $request->get('refreshToken')));
            return response()->json($token->toArray())
                ->cookie(new Cookie(
                    name: 'accessToken',
                    value: $token->getAccessToken(),
                    expire: now()->addSeconds($token->getAccessTokenLifeTime()),
                    path: '/',
                ))
                ->cookie(new Cookie(
                    name: 'refreshToken',
                    value: $token->getRefreshToken(),
                    path: '/',
                ))
                ;
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
