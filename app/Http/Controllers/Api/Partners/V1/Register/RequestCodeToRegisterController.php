<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Partners\V1\Register;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partners\Register\RequestCodeToRegisterRequest;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use MarketPlace\Partners\Auth\Application\Service\AuthorizeService;
use MarketPlace\Partners\User\Infrastructure\Exception\PhoneAlreadyRegisteredException;
use MarketPlace\Partners\User\Infrastructure\Exception\RequestCodeThrottlingException;

class RequestCodeToRegisterController extends Controller
{
    private AuthorizeService $service;

    public function __construct(AuthorizeService $service)
    {
        $this->service = $service;
    }

    public function __invoke(RequestCodeToRegisterRequest $request, Response $response): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->service->requestCodeToRegister('KG', $request->get('phone'));
            DB::commit();
            return response()->json(['message' => 'ok']);
        } catch (PhoneAlreadyRegisteredException $e) {
            DB::rollBack();
            throw ValidationException::withMessages(['phone' => ['Телефон номер уже зарегистрирован']]);
        } catch (RequestCodeThrottlingException $e) {
            DB::rollBack();
            throw ValidationException::withMessages(['phone' => ['Повторная отправка возможно через 1 минута']]);
        }
    }
}
