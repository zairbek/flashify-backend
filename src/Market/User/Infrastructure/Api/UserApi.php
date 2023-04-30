<?php

declare(strict_types=1);

namespace MarketPlace\Market\User\Infrastructure\Api;

use App;
use MarketPlace\Market\User\Application\Dto\CreateUserFromPhoneDto;
use MarketPlace\Market\User\Application\Dto\CreateUserPhoneDto;
use MarketPlace\Market\User\Application\Dto\FindUserPhoneDto;
use MarketPlace\Market\User\Application\Dto\UpdateUserPhoneDto;
use MarketPlace\Market\User\Application\Service\UserPhoneService;
use MarketPlace\Market\User\Application\Service\UserService;
use MarketPlace\Market\User\Infrastructure\Exception\UserPhoneNumberNotFoundException;

class UserApi
{
    private UserPhoneService $userPhoneService;
    private UserService $userService;

    public function __construct()
    {
        $this->userPhoneService = App::make(UserPhoneService::class);
        $this->userService = App::make(UserService::class);
    }

    public function findUserPhone(string $regionIsoCode, string $number): array
    {
        try {
            return $this->userPhoneService->findUserPhone(new FindUserPhoneDto($regionIsoCode, $number));
        } catch (UserPhoneNumberNotFoundException $exception) {
            return [];
        }
    }

    public function updateUserPhone(array $data): void
    {
        $this->userPhoneService->updateUserPhone(new UpdateUserPhoneDto(
            uuid: $data['uuid'],
            regionIsoCode: $data['phone']['regionIsoCode'],
            phoneNumber: $data['phone']['number'],
            createdAt: $data['createdAt'],
            userId: $data['userId'],
            confirmationCode: $data['confirmationCode'],
            sendAt: $data['sendAt']
        ));
    }

    public function createUserViaPhone(array $data): void
    {
        $this->userPhoneService->createUser(new CreateUserFromPhoneDto(
            uuid: $data['uuid'],
            login: $data['login'],
            phoneUuid: $data['phoneUuid'],
        ));
    }

    public function createUserPhone(array $data): void
    {
        $this->userPhoneService->createUserPhone(new CreateUserPhoneDto(
            uuid: $data['uuid'],
            regionIsoCode: $data['phone']['regionIsoCode'],
            phoneNumber: $data['phone']['number'],
            createdAt: $data['createdAt'],
            userId: $data['userId'],
            confirmationCode: $data['confirmationCode'],
            sendAt: $data['sendAt']
        ));
    }
}
