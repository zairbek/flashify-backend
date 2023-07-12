<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Partners\V1\Me\Email;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partners\Me\Email\RequestCodeToChangeEmailRequest;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use MarketPlace\Partners\Auth\Infrastructure\Exception\UserNotFoundException;
use MarketPlace\Partners\User\Application\Service\UserService;
use MarketPlace\Partners\User\Infrastructure\Exception\EmailAlreadyRegisteredException;
use MarketPlace\Partners\User\Infrastructure\Exception\RequestCodeThrottlingException;
use MarketPlace\Partners\User\Infrastructure\Exception\UserUnauthenticatedException;

class RequestCodeToChangeEmailController extends Controller
{
    private UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function __invoke(RequestCodeToChangeEmailRequest $request, Response $response): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->service->requestCodeToChangeEmail($request->get('email'));
            DB::commit();
            return response()->json(['message' => 'ok']);
        } catch (RequestCodeThrottlingException $e) {
            DB::rollBack();
            throw ValidationException::withMessages(['email' => ['Повторная отправка возможно через 1 минута']]);
        } catch (EmailAlreadyRegisteredException $e) {
            DB::rollBack();
            throw ValidationException::withMessages(['email' => ['Email уже используется']]);
        } catch (UserNotFoundException $e) {
            DB::rollBack();
            throw ValidationException::withMessages(['email' => ['Пользователь не найден']]);
        } catch (UserUnauthenticatedException $e) {
            return response()->json(['errors' => $e->getMessage()], 401);
        }
    }
}
