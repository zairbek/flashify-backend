<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Partners\V1\Me\Email;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partners\Me\Email\ChangeEmailRequest;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use MarketPlace\Partners\User\Application\Dto\ChangeEmailDto;
use MarketPlace\Partners\User\Application\Service\UserService;
use MarketPlace\Partners\User\Infrastructure\Exception\ConfirmationCodeIncorrectException;
use MarketPlace\Partners\User\Infrastructure\Exception\RequestCodeNotFoundException;
use MarketPlace\Partners\User\Infrastructure\Exception\UserNotFoundException;
use MarketPlace\Partners\User\Infrastructure\Exception\UserUnauthenticatedException;
use Throwable;

class ChangeEmailController extends Controller
{
    private UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * @throws ValidationException|Throwable
     */
    public function __invoke(ChangeEmailRequest $request, Response $response): JsonResponse
    {
        DB::beginTransaction();

        try {
            $this->service->changeEmail(new ChangeEmailDto(
                email: $request->get('email'),
                confirmationCode: $request->get('code')
            ));
            DB::commit();
            return response()->json(['message' => 'ok']);
        } catch (ConfirmationCodeIncorrectException|RequestCodeNotFoundException $e) {
            DB::rollBack();
            throw ValidationException::withMessages(['code' => ['Неправильный код подтверждение']]);
        } catch (UserNotFoundException|UserUnauthenticatedException $e) {
            DB::rollBack();
            return response()->json(['message' => $e], 400);
        }
    }
}
