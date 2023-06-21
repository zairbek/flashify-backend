<?php

declare(strict_types=1);

namespace MarketPlace\Partners\User\Infrastructure\Api;

use App;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Partners\User\Application\Service\UserService;
use MarketPlace\Partners\User\Infrastructure\Exception\UserNotFoundException;

class UserApi
{
    private UserService $accountService;

    public function __construct()
    {
        $this->accountService = App::make(UserService::class);
    }

    public function findAccount(string $id): array
    {
        try {
            $user = $this->accountService->find(new Uuid($id));

            return [
                'uuid' => $user->getUuid()->getId(),
                'login' => $user->getLogin()->getLogin(),
                'email' => $user->getEmail()?->toArray(),
                'phone' => $user->getPhone() ? $user->getPhone()->toArray() : null,
                'userName' => [
                    'firstName' => $user->getUserName()?->getFirstName(),
                    'lastName' => $user->getUserName()?->getLastName(),
                ],
                'sex' => $user->getSex()?->getSex(),
                'status' => $user->getStatus()->getStatus()
            ];
        } catch (UserNotFoundException $e) {
            return [];
        }
    }
}
