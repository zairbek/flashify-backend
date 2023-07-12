<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Partners\V1\Me\Phone;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partners\Me\Phone\RequestCodeToChangePhoneRequest;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use MarketPlace\Partners\Auth\Infrastructure\Exception\UserNotFoundException;
use MarketPlace\Partners\User\Application\Service\UserService;
use MarketPlace\Partners\User\Infrastructure\Exception\RequestCodeThrottlingException;
use MarketPlace\Partners\User\Infrastructure\Exception\UserUnauthenticatedException;

class RequestCodeToChangePhoneController extends Controller
{
    private UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function __invoke(RequestCodeToChangePhoneRequest $request, Response $response): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->service->requestCodeToChangePhone('KG', $request->get('phone'));
            DB::commit();
            return response()->json(['message' => 'ok']);
        } catch (RequestCodeThrottlingException $e) {
            DB::rollBack();
            throw ValidationException::withMessages(['email' => ['Повторная отправка возможно через 1 минута']]);
        } catch (UserNotFoundException $e) {
            DB::rollBack();
            throw ValidationException::withMessages(['email' => ['Пользователь не найден']]);
        } catch (UserUnauthenticatedException $e) {
            return response()->json(['errors' => $e->getMessage()], 401);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }
}
