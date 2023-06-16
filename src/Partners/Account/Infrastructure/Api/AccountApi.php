<?php

declare(strict_types=1);

namespace MarketPlace\Partners\Account\Infrastructure\Api;

use App;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Partners\Account\Application\Service\AccountService;
use MarketPlace\Partners\Account\Infrastructure\Exception\AccountNotFoundException;

class AccountApi
{
    private AccountService $accountService;

    public function __construct()
    {
        $this->accountService = App::make(AccountService::class);
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
        } catch (AccountNotFoundException $e) {
            return [];
        }
    }
}
