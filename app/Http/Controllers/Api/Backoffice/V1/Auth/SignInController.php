<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Backoffice\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\Auth\SignInRequest;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use JsonException;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use MarketPlace\Backoffice\Auth\Application\Dto\SignInDto;
use MarketPlace\Backoffice\Auth\Application\Service\AuthorizeService;
use MarketPlace\Backoffice\Auth\Infrastructure\Exception\NotGivenClientIdAndSecretForTokenServiceException;
use MarketPlace\Backoffice\Auth\Infrastructure\Exception\UserCredentialsIncorrectException;
use MarketPlace\Common\Domain\Exceptions\UserIsBannedException;
use MarketPlace\Common\Domain\Exceptions\UserIsInactiveException;
use Symfony\Component\HttpFoundation\Cookie;
use Throwable;

class SignInController extends Controller
{
    private AuthorizeService $service;

    public function __construct(AuthorizeService $service)
    {
        $this->service = $service;
    }

    /**
     * @throws ValidationException|Throwable
     */
    public function __invoke(SignInRequest $request, Response $response): JsonResponse
    {
        DB::beginTransaction();

        try {
            $token = $this->service->signIn(new SignInDto(
                email: $request->get('email'),
                password: $request->get('password')
            ));
            DB::commit();
            return response()->json($token->toArray())
                ->cookie(new Cookie(
                    name: 'accessToken',
                    value: $token->getAccessToken(),
                    expire: now()->addSeconds($token->getAccessTokenLifeTime()),
                    path: '/',
                    httpOnly: false
                ))
                ->cookie(new Cookie(
                    name: 'refreshToken',
                    value: $token->getRefreshToken(),
                    path: '/',
                ))
                ;
        } catch (UserCredentialsIncorrectException $e) {
            DB::rollBack();
            throw ValidationException::withMessages(['email' => ['Неверный логин или пароль']]);
        } catch (UserIsBannedException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Аккаунт пользователя забанен'], Response::HTTP_FORBIDDEN);
        } catch (UserIsInactiveException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Аккаунт пользователя неактивен'], Response::HTTP_FORBIDDEN);
        } catch (
            OAuthServerException
            | NotGivenClientIdAndSecretForTokenServiceException
            | UniqueTokenIdentifierConstraintViolationException
            | JsonException
            | Exception $e
        ) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
