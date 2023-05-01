<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Market\V1\Auth\Phone;

use App\Http\Controllers\Controller;
use App\Http\Requests\Market\Auth\Phone\RequestCodeToPhoneNumberRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use MarketPlace\Market\Auth\Application\Dto\SendCodeForSignInViaPhoneDto;
use MarketPlace\Market\Auth\Application\Service\AuthorizeService;
use MarketPlace\Market\Auth\Infrastructure\Exception\SendSmsThrottleException;

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
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }
}
