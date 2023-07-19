<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Partners\V1\Register;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partners\Register\RegisterRequest;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use MarketPlace\Partners\Auth\Application\Dto\RegisterDto;
use MarketPlace\Partners\Auth\Application\Service\AuthorizeService;
use MarketPlace\Partners\Auth\Infrastructure\Exception\ConfirmationCodeIsNotMatchException;
use MarketPlace\Partners\User\Infrastructure\Exception\PhoneAlreadyRegisteredException;
use Webmozart\Assert\InvalidArgumentException;

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
                phoneRegionCode: 'KG',
                phone: $request->get('phone'),
                code: $request->get('code'),
            ));
            DB::commit();
            return response()->json($token->toArray());
        } catch (InvalidArgumentException $e) {
            DB::rollBack();
            throw ValidationException::withMessages(['phone' => ['Невалидный номер телефона']]);
        } catch (ConfirmationCodeIsNotMatchException $e) {
            DB::rollBack();
            throw ValidationException::withMessages(['code' => ['Неправильный код подтверждение']]);
        } catch (PhoneAlreadyRegisteredException $e) {
            DB::rollBack();
            throw ValidationException::withMessages(['phone' => ['Телефон номер уже зарегистрирован']]);
        }
    }
}
