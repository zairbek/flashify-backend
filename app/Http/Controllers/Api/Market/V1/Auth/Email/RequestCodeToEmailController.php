<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Market\V1\Auth\Email;

use App\Http\Controllers\Controller;
use App\Http\Requests\Market\Auth\Email\RequestCodeToEmailRequest;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use MarketPlace\Market\Auth\Application\Dto\SendCodeForSignInViaEmailDto;
use MarketPlace\Market\Auth\Application\Service\AuthorizeService;
use MarketPlace\Market\Auth\Infrastructure\Exception\SendSmsThrottleException;
use MarketPlace\Market\Auth\Infrastructure\Exception\UserEmailNotFoundException;
use Throwable;

class RequestCodeToEmailController extends Controller
{
    private AuthorizeService $service;

    public function __construct(AuthorizeService $service)
    {
        $this->service = $service;
    }

    /**
     * @throws ValidationException
     * @throws Throwable
     */
    public function __invoke(RequestCodeToEmailRequest $request, Response $response): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->service->sendCodeForSignInViaEmail(new SendCodeForSignInViaEmailDto(email: $request->get('email')));

            DB::commit();
            return response()->json(['message' => 'ok']);
        } catch (SendSmsThrottleException $e) {
            DB::rollBack();
            throw ValidationException::withMessages(['email' => ['Повторная отправка возможно через 1 минута']]);
        } catch (UserEmailNotFoundException $e) {
            DB::rollBack();
            throw ValidationException::withMessages(['email' => ['Пользователь не найден']]);
        } catch (Exception) {
            DB::rollBack();
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }
}
