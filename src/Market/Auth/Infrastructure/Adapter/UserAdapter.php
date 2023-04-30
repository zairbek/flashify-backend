<?php

declare(strict_types=1);

namespace MarketPlace\Market\Auth\Infrastructure\Adapter;

use LogicException;
use MarketPlace\Common\Domain\ValueObject\ConfirmationCode;
use MarketPlace\Common\Domain\ValueObject\CreatedAt;
use MarketPlace\Common\Domain\ValueObject\Phone;
use MarketPlace\Common\Domain\ValueObject\SendAt;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Common\Infrastructure\Service\Hydrator;
use MarketPlace\Market\Auth\Domain\Adapter\UserAdapterInterface;
use MarketPlace\Market\Auth\Domain\Entity\PhoneNumber;
use MarketPlace\Market\Auth\Domain\Entity\User;
use MarketPlace\Market\Auth\Domain\Exception\UserPhoneNotFoundException;
use MarketPlace\Market\Auth\Domain\ValueObject\Token;
use MarketPlace\Market\Auth\Domain\ValueObject\UserId;
use MarketPlace\Market\User\Infrastructure\Api\UserApi;

class UserAdapter implements UserAdapterInterface
{
    private UserApi $api;
    private Hydrator $hydrator;

    public function __construct()
    {
        $this->api = new UserApi();
        $this->hydrator = new Hydrator();
    }

    public function existsUserByPhone(Phone $phone): bool
    {
        // TODO: Implement existsUserByPhone() method.
    }

    /**
     * @param Phone $phone
     * @return PhoneNumber
     * @throws UserPhoneNotFoundException
     * @throws \Exception
     */
    public function findUserPhone(Phone $phone): PhoneNumber
    {
        $userPhoneNumber = $this->api->findUserPhone($phone->getRegionCode(), $phone->toString());

        if (empty($userPhoneNumber)) {
            throw new UserPhoneNotFoundException();
        }

        return $this->hydrator->hydrate(PhoneNumber::class, [
            'uuid' => new Uuid($userPhoneNumber['uuid']),
            'phone' => Phone::fromString($userPhoneNumber['phone']['regionIsoCode'], $userPhoneNumber['phone']['number']),
            'createdAt' => CreatedAt::fromIsoFormat($userPhoneNumber['createdAt']),
            'userId' => $userPhoneNumber['userId'] ? new UserId($userPhoneNumber['userId']) : null,
            'confirmationCode' => $userPhoneNumber['confirmationCode'] ? new ConfirmationCode($userPhoneNumber['confirmationCode']) : null,
            'sendAt' => $userPhoneNumber['sendAt'] ? SendAt::fromIsoFormat($userPhoneNumber['sendAt']) : null,
        ]);
    }

    public function createUserPhone(PhoneNumber $userPhone): void
    {
        $this->api->createUserPhone([
            'uuid' => $userPhone->getUuid()->getId(),
            'phone' => [
                'regionIsoCode' => $userPhone->getPhone()->getRegionCode(),
                'number' => $userPhone->getPhone()->toString()
            ],
            'createdAt' => $userPhone->getCreatedAt()->toIsoFormat(),
            'userId' => $userPhone->getUserId()?->getUserId(),
            'confirmationCode' => $userPhone->getConfirmationCode()?->getCode(),
            'sendAt' => $userPhone->getSendAt()?->toIsoFormat(),
        ]);
    }

    public function updateUserPhone(PhoneNumber $userPhone): void
    {
        $this->api->updateUserPhone([
            'uuid' => $userPhone->getUuid()->getId(),
            'phone' => [
                'regionIsoCode' => $userPhone->getPhone()->getRegionCode(),
                'number' => $userPhone->getPhone()->toString()
            ],
            'createdAt' => $userPhone->getCreatedAt()->toIsoFormat(),
            'userId' => $userPhone->getUserId()?->getUserId(),
            'confirmationCode' => $userPhone->getConfirmationCode()?->getCode(),
            'sendAt' => $userPhone->getSendAt()?->toIsoFormat(),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function findUser(UserId $getUserId): User
    {
        // TODO: Implement findUser() method.
    }

    public function authorize(User $user): Token
    {
        return new Token('d', 'e', 'd', 'd');
    }

    public function createUserViaPhone(User $user): void
    {
        if (is_null($user->getPhone())){
            throw new LogicException('При создание пользователя через номер телефона произошла ошибка.');
        }

        $this->api->createUserViaPhone([
            'uuid' => $user->getId()->getId(),
            'login' => $user->getLogin()->getLogin(),
            'phoneUuid' => $user->getPhone()->getUuid()->getId(),
        ]);
    }
}
