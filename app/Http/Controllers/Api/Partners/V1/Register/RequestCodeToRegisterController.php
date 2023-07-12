<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Partners\V1\Register;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partners\Me\Phone\RequestCodeToChangePhoneRequest;
use App\Http\Requests\Partners\Register\RequestCodeToRegisterRequest;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use MarketPlace\Partners\Auth\Infrastructure\Exception\UserNotFoundException;
use MarketPlace\Partners\User\Application\Service\UserService;
use MarketPlace\Partners\User\Infrastructure\Exception\PhoneAlreadyRegisteredException;
use MarketPlace\Partners\User\Infrastructure\Exception\RequestCodeThrottlingException;
use MarketPlace\Partners\User\Infrastructure\Exception\UserUnauthenticatedException;

class RequestCodeToRegisterController extends Controller
{
    private UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function __invoke(RequestCodeToRegisterRequest $request, Response $response): JsonResponse
    {
//        DB::beginTransaction();
//        try {
            $this->service->requestCodeToRegister('KG', $request->get('phone'));
//            DB::commit();
            return response()->json(['message' => 'ok']);
//        } catch (RequestCodeThrottlingException $e) {
//            DB::rollBack();
//            throw ValidationException::withMessages(['phone' => ['Повторная отправка возможно через 1 минута']]);
//        } catch (PhoneAlreadyRegisteredException $e) {
//            DB::rollBack();
//            throw ValidationException::withMessages(['phone' => ['Телефон номер уже зарегистрирован']]);
//        } catch (UserNotFoundException $e) {
//            DB::rollBack();
//            throw ValidationException::withMessages(['phone' => ['Пользователь не найден']]);
//        } catch (UserUnauthenticatedException $e) {
//            return response()->json(['errors' => $e->getMessage()], 401);
//        }
    }
}
