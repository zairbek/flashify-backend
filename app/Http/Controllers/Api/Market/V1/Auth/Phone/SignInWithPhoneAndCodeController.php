<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Market\V1\Auth\Phone;

use App\Http\Controllers\Controller;
use App\Http\Requests\Market\Auth\Phone\SignInWithPhoneAndCodeRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use MarketPlace\Common\Domain\Exceptions\UserIsBannedException;
use MarketPlace\Common\Domain\Exceptions\UserIsInactiveException;
use MarketPlace\Market\Auth\Application\Dto\SendCodeForSignInViaPhoneDto;
use MarketPlace\Market\Auth\Application\Dto\SignInWithPhoneDto;
use MarketPlace\Market\Auth\Domain\Exception\ConfirmationCodeIsNotMatchException;
use MarketPlace\Market\Auth\Domain\Exception\SendSmsThrottleException;
use MarketPlace\Market\Auth\Domain\Exception\UserNotFoundException;
use MarketPlace\Market\Auth\Domain\Exception\UserPhoneNotFoundException;
use MarketPlace\Market\Auth\Domain\Service\AuthorizeService;

class SignInWithPhoneAndCodeController extends Controller
{
    private AuthorizeService $service;

    public function __construct(AuthorizeService $service)
    {
        $this->service = $service;
    }

    /**
     * @throws ValidationException
     */
    public function __invoke(SignInWithPhoneAndCodeRequest $request, Response $response): JsonResponse
    {
        try {
            $token = $this->service->signInWithPhone(new SignInWithPhoneDto(
                regionIsoCode: 'KG',
                phone: $request->get('phone'),
                confirmationCode: $request->get('code')
            ));
        } catch (UserIsBannedException $e) {
        } catch (UserIsInactiveException $e) {
        } catch (ConfirmationCodeIsNotMatchException $e) {
        } catch (UserNotFoundException $e) {
        } catch (UserPhoneNotFoundException $e) {
        }

        return response()->json($token->toArray());

//        try {
//        } catch (SendSmsThrottleException $e) {
//            throw ValidationException::withMessages(['phone' => ['Повторная отправка возможно через 1 минута']]);
//        } catch (Exception $e) {
//            return response()->json(['errors' => $e->getMessage()], 400);
//        }
    }
}
