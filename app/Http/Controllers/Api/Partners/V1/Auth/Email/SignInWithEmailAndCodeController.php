<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Partners\V1\Auth\Email;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partners\Auth\Email\SignInWithEmailAndCodeRequest;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use MarketPlace\Common\Domain\Exceptions\UserIsBannedException;
use MarketPlace\Common\Domain\Exceptions\UserIsInactiveException;
use MarketPlace\Partners\Auth\Application\Dto\SignInWithEmailDto;
use MarketPlace\Partners\Auth\Application\Service\AuthorizeService;
use MarketPlace\Partners\Auth\Infrastructure\Exception\ConfirmationCodeIsNotMatchException;
use MarketPlace\Partners\Auth\Infrastructure\Exception\UserNotFoundException;
use Throwable;

class SignInWithEmailAndCodeController extends Controller
{
    private AuthorizeService $service;

    public function __construct(AuthorizeService $service)
    {
        $this->service = $service;
    }

    /**
     * @throws ValidationException|Throwable
     */
    public function __invoke(SignInWithEmailAndCodeRequest $request, Response $response): JsonResponse
    {
        DB::beginTransaction();

        try {
            $token = $this->service->signInWithEmail(new SignInWithEmailDto(
                email: $request->get('email'),
                confirmationCode: $request->get('code')
            ));
            DB::commit();
            return response()->json($token->toArray());
        } catch (UserIsBannedException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Аккаунт пользователя забанен'], Response::HTTP_FORBIDDEN);
        } catch (UserIsInactiveException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Аккаунт пользователя неактивен'], Response::HTTP_FORBIDDEN);
        } catch (ConfirmationCodeIsNotMatchException $e) {
            DB::rollBack();
            throw ValidationException::withMessages(['code' => ['Неправильный код подтверждение']]);
        } catch (UserNotFoundException $e) {
            DB::rollBack();
            throw ValidationException::withMessages(['email' => ['phone not found']]);
        }
    }
}
