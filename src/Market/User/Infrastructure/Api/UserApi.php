<?php

declare(strict_types=1);

namespace MarketPlace\Market\User\Infrastructure\Api;

use MarketPlace\Market\User\Application\Dto\FindUserPhoneDto;
use MarketPlace\Market\User\Application\Dto\UpdateUserPhoneDto;
use MarketPlace\Market\User\Domain\Service\UserPhoneService;
use MarketPlace\Market\User\Infrastructure\Exception\UserPhoneNumberNotFoundException;

class UserApi
{
    private UserPhoneService $userPhoneService;

    public function __construct()
    {
        $this->userPhoneService = \App::make(UserPhoneService::class);
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
}
