<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Partners\V1\Auth\Phone;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partners\Auth\Phone\SignInWithPhoneAndCodeRequest;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use JsonException;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use MarketPlace\Common\Domain\Exceptions\UserIsBannedException;
use MarketPlace\Common\Domain\Exceptions\UserIsInactiveException;
use MarketPlace\Partners\Auth\Application\Dto\SignInWithPhoneDto;
use MarketPlace\Partners\Auth\Application\Service\AuthorizeService;
use MarketPlace\Partners\Auth\Infrastructure\Exception\ConfirmationCodeIsNotMatchException;
use MarketPlace\Partners\Auth\Infrastructure\Exception\UserNotFoundException;
use MarketPlace\Partners\Auth\Infrastructure\Exception\UserPhoneNotFoundException;

class SignInWithPhoneAndCodeController extends Controller
{
    private AuthorizeService $service;

    public function __construct(AuthorizeService $service)
    {
        $this->service = $service;
    }

    /**
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
        } catch (UniqueTokenIdentifierConstraintViolationException|OAuthServerException|JsonException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (UserIsBannedException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Аккаунт пользователя забанен'], Response::HTTP_FORBIDDEN);
        } catch (UserIsInactiveException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Аккаунт пользователя неактивен'], Response::HTTP_FORBIDDEN);
        } catch (ConfirmationCodeIsNotMatchException $e) {
            DB::rollBack();
            throw ValidationException::withMessages(['code' => ['Неправильный код подтверждение']]);
        } catch (UserNotFoundException|UserPhoneNotFoundException $e) {
            DB::rollBack();
            throw ValidationException::withMessages(['phone' => ['phone not found']]);
        }
    }
}
