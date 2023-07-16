<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Partners\V1\Register;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partners\Me\Email\ChangeEmailRequest;
use App\Http\Requests\Partners\Me\Phone\ChangePhoneRequest;
use App\Http\Requests\Partners\Register\RegisterRequest;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use MarketPlace\Partners\Auth\Application\Dto\RegisterDto;
use MarketPlace\Partners\Auth\Application\Service\AuthorizeService;
use MarketPlace\Partners\Auth\Infrastructure\Exception\ConfirmationCodeIsNotMatchException;
use MarketPlace\Partners\User\Application\Dto\ChangeEmailDto;
use MarketPlace\Partners\User\Application\Dto\ChangePhoneDto;
use MarketPlace\Partners\User\Application\Service\UserService;
use MarketPlace\Partners\User\Infrastructure\Exception\ConfirmationCodeIncorrectException;
use MarketPlace\Partners\User\Infrastructure\Exception\PhoneAlreadyRegisteredException;
use MarketPlace\Partners\User\Infrastructure\Exception\RequestCodeNotFoundException;
use MarketPlace\Partners\User\Infrastructure\Exception\UserNotFoundException;
use MarketPlace\Partners\User\Infrastructure\Exception\UserUnauthenticatedException;
use Throwable;

class RegisterController extends Controller
{
    private AuthorizeService $service;

    public function __construct(AuthorizeService $service)
    {
        $this->service = $service;
    }

    public function __invoke(RegisterRequest $request, Response $response): JsonResponse
    {
        DB::beginTransaction();

        try {
            $token = $this->service->register(new RegisterDto(
                firstName: $request->get('firstName'),
                lastName: $request->get('firstName'),
                phoneRegionCode: 'KG',
                phone: $request->get('phone'),
                code: $request->get('code'),
                password: $request->get('password'),
            ));
            DB::commit();
            return response()->json($token->toArray());
        } catch (\JsonException $e) {
        } catch (UniqueTokenIdentifierConstraintViolationException $e) {
        } catch (OAuthServerException $e) {
        } catch (ConfirmationCodeIsNotMatchException $e) {
            DB::rollBack();
            throw ValidationException::withMessages(['code' => ['Неправильный код подтверждение']]);
        } catch (PhoneAlreadyRegisteredException $e) {
            DB::rollBack();
            throw ValidationException::withMessages(['phone' => ['Телефон номер уже зарегистрирован']]);
        }
    }
}
