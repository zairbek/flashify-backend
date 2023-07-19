<?php

declare(strict_types=1);

namespace MarketPlace\Partners\User\Infrastructure\Api;

use App;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Partners\User\Application\Dto\CreateUserDto;
use MarketPlace\Partners\User\Application\Dto\FindUserByPhoneDto;
use MarketPlace\Partners\User\Application\Dto\UpdateUserDto;
use MarketPlace\Partners\User\Application\Dto\UpdateUserEmailDto;
use MarketPlace\Partners\User\Application\Dto\UpdateUserPhoneDto;
use MarketPlace\Partners\User\Application\Service\UserService;
use MarketPlace\Partners\User\Domain\Entity\User;
use MarketPlace\Partners\User\Infrastructure\Exception\RequestCodeThrottlingException;
use MarketPlace\Partners\User\Infrastructure\Exception\UserNotFoundException;

class UserApi
{
    private UserService $userService;

    public function __construct()
    {
        $this->userService = App::make(UserService::class);
    }

    public function create(array $data): void
    {
        $this->userService->create(new CreateUserDto(
            uuid: $data['uuid'],
            login: $data['login'],
            phoneDto: $data['phone']
                ? new UpdateUserPhoneDto(
                    regionCode: $data['phone']['regionIsoCode'],
                    phone: $data['phone']['number'],
                    code: $data['phone']['code'],
                    sendAt: $data['phone']['sendAt'],
                )
                : null,
            emailDto: $data['email']
                ? new UpdateUserEmailDto(
                    email: $data['email']['email'],
                    code: $data['email']['code'],
                    sendAt: $data['email']['sendAt'],
                )
                : null,
            firstName: $data['userName']['firstName'],
            lastName: $data['userName']['lastName'],
            status: $data['status']
        ));
    }

    /**
     * @throws UserNotFoundException
     */
    public function update(array $data): void
    {
        $this->userService->update(new UpdateUserDto(
            uuid: $data['uuid'],
            login: $data['login'],
            phoneDto: $data['phone']
                ? new UpdateUserPhoneDto(
                    regionCode: $data['phone']['regionIsoCode'],
                    phone: $data['phone']['number'],
                    code: $data['phone']['code'],
                    sendAt: $data['phone']['sendAt'],
                )
                : null,
            emailDto: $data['email']
                ? new UpdateUserEmailDto(
                    email: $data['email']['email'],
                    code: $data['email']['code'],
                    sendAt: $data['email']['sendAt'],
                )
                : null,
            firstName: $data['userName']['firstName'],
            lastName: $data['userName']['lastName'],
            status: $data['status']
        ));
    }

    /**
     * @throws RequestCodeThrottlingException
     */
    public function requestCodeForRegister(string $getRegionCode, string $toString): void
    {
        $this->userService->requestCodeForRegister($getRegionCode, $toString);
    }

    public function isConfirmationCodeCorrect(array $data): bool
    {
        return $this->userService->isConfirmationCodeCorrect($data);
    }

    public function clearConfirmationCode(array $data): void
    {
        $this->userService->clearConfirmationCode($data);
    }

    public function findByPhone(string $regionIsoCode, string $number): array
    {
        try {
            $user = $this->userService->findByPhone(new FindUserByPhoneDto($regionIsoCode, $number));

            return $this->toArray($user);
        } catch (UserNotFoundException $e) {
            return [];
        }
    }

    public function findByEmail(string $email): array
    {
        try {
            $user = $this->userService->findByEmail($email);

            return $this->toArray($user);
        } catch (UserNotFoundException $e) {
            return [];
        }
    }

    public function findUser(string $id): array
    {
        try {
            $user = $this->userService->find(new Uuid($id));

            return $this->toArray($user);
        } catch (UserNotFoundException $e) {
            return [];
        }
    }

    private function toArray(User $user): array
    {
        return [
            'uuid' => $user->getUuid()->getId(),
            'login' => $user->getLogin()->getLogin(),
            'email' => $user->getEmail()
                ? [
                    'email' => $user->getEmail()->getEmail(),
                    'code' => $user->getEmail()->getCode(),
                    'sendAt' => $user->getEmail()->getSendAt()?->toIsoFormat(),
                ]
                : null,
            'phone' => $user->getPhone()
                ? [
                    'regionIsoCode' => $user->getPhone()->getRegionCode(),
                    'number' => $user->getPhone()->toString(),
                    'code' => $user->getPhone()->getCode(),
                    'sendAt' => $user->getPhone()->getSendAt()?->toIsoFormat(),
                ]
                : null,
            'userName' => [
                'firstName' => $user->getAccountName()?->getFirstName(),
                'lastName' => $user->getAccountName()?->getLastName(),
            ],
            'status' => $user->getStatus()->getStatus()
        ];
    }
}
