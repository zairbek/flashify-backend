<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\User\Infrastructure\Api;

use App;
use MarketPlace\Backoffice\Auth\Application\Dto\GetByCredentialsDto;
use MarketPlace\Backoffice\User\Application\Service\UserService;
use MarketPlace\Backoffice\User\Infrastructure\Exception\UserCredentialsIncorrectException;
use MarketPlace\Backoffice\User\Infrastructure\Exception\UserNotFoundException;
use MarketPlace\Common\Domain\ValueObject\Email;
use MarketPlace\Common\Domain\ValueObject\Password;
use MarketPlace\Common\Domain\ValueObject\Uuid;

class UserApi
{
    private UserService $userService;

    public function __construct()
    {
        $this->userService = App::make(UserService::class);
    }

    public function findUser(string $id): array
    {
        try {
            return $this->userService->find(new Uuid($id))->toArray();
        } catch (UserNotFoundException $e) {
            return [];
        }
    }

    public function getByCredentials(string $email, string $password): array
    {
        $dto = new GetByCredentialsDto($email, $password);

        try {
            return $this->userService->getByCredentials($dto)->toArray();
        } catch (UserCredentialsIncorrectException $e) {
            return [];
        }
    }
}
