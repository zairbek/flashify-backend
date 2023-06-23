<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Partners\V1\Auth\Phone;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partners\Auth\Phone\RequestCodeToPhoneNumberRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use MarketPlace\Partners\Auth\Application\Dto\SendCodeForSignInViaPhoneDto;
use MarketPlace\Partners\Auth\Application\Service\AuthorizeService;
use MarketPlace\Partners\Auth\Infrastructure\Exception\SendSmsThrottleException;
use MarketPlace\Partners\Auth\Infrastructure\Exception\UserNotFoundException;
use MarketPlace\Partners\Auth\Infrastructure\Exception\UserPhoneNotFoundException;

class RequestCodeToPhoneNumberController extends Controller
{
    private AuthorizeService $service;

    public function __construct(AuthorizeService $service)
    {
        $this->service = $service;
    }

    /**
     * @throws ValidationException
     */
    public function __invoke(RequestCodeToPhoneNumberRequest $request, Response $response): JsonResponse
    {
        try {
            $this->service->sendCodeForSignInViaPhone(new SendCodeForSignInViaPhoneDto(
                regionIsoCode: 'KG',
                phone: $request->get('phone')
            ));
            return response()->json(['message' => 'ok']);
        } catch (SendSmsThrottleException $e) {
            throw ValidationException::withMessages(['phone' => ['Повторная отправка возможно через 1 минута']]);
        } catch (UserPhoneNotFoundException|UserNotFoundException $e) {
            throw ValidationException::withMessages(['phone' => ['Пользователь не найден']]);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }
}
