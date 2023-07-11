<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Partners\V1\Me\Email;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partners\Me\Email\ChangeEmailRequest;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use MarketPlace\Common\Domain\Exceptions\UserIsBannedException;
use MarketPlace\Common\Domain\Exceptions\UserIsInactiveException;
use MarketPlace\Partners\Auth\Application\Dto\SignInWithEmailDto;
use MarketPlace\Partners\Auth\Infrastructure\Exception\ConfirmationCodeIsNotMatchException;
use MarketPlace\Partners\Auth\Infrastructure\Exception\UserNotFoundException;
use MarketPlace\Partners\User\Application\Service\UserService;
use Throwable;

class ChangeEmailController extends Controller
{
    private UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * @throws ValidationException|Throwable
     */
    public function __invoke(ChangeEmailRequest $request, Response $response): JsonResponse
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
