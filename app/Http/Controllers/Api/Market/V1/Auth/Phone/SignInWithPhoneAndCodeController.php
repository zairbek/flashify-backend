<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Market\V1\Auth\Phone;

use App\Http\Controllers\Controller;
use App\Http\Requests\Market\Auth\Phone\SignInWithPhoneAndCodeRequest;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use MarketPlace\Common\Domain\Exceptions\UserIsBannedException;
use MarketPlace\Common\Domain\Exceptions\UserIsInactiveException;
use MarketPlace\Market\Auth\Application\Dto\SignInWithPhoneDto;
use MarketPlace\Market\Auth\Application\Service\AuthorizeService;
use MarketPlace\Market\Auth\Infrastructure\Exception\ConfirmationCodeIsNotMatchException;
use MarketPlace\Market\Auth\Infrastructure\Exception\UserNotFoundException;
use MarketPlace\Market\Auth\Infrastructure\Exception\UserPhoneNotFoundException;
use Throwable;

class SignInWithPhoneAndCodeController extends Controller
{
    private AuthorizeService $service;

    public function __construct(AuthorizeService $service)
    {
        $this->service = $service;
    }

    /**
     * @param SignInWithPhoneAndCodeRequest $request
     * @param Response $response
     * @return JsonResponse
     * @throws ValidationException
     * @throws Throwable
     */
    public function __invoke(SignInWithPhoneAndCodeRequest $request, Response $response): JsonResponse
    {
        DB::beginTransaction();

        try {
            $token = $this->service->signInWithPhone(new SignInWithPhoneDto(
                regionIsoCode: 'KG',
                phone: $request->get('phone'),
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
        } catch (UserPhoneNotFoundException|UserNotFoundException $e) {
            DB::rollBack();
            throw ValidationException::withMessages(['phone' => ['phone not found']]);
        }
    }
}
