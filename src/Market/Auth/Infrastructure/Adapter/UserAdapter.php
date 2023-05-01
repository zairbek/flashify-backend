<?php

declare(strict_types=1);

namespace MarketPlace\Market\Auth\Infrastructure\Adapter;

use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use LogicException;
use MarketPlace\Common\Domain\ValueObject\ConfirmationCode;
use MarketPlace\Common\Domain\ValueObject\CreatedAt;
use MarketPlace\Common\Domain\ValueObject\Email;
use MarketPlace\Common\Domain\ValueObject\Login;
use MarketPlace\Common\Domain\ValueObject\PersonName;
use MarketPlace\Common\Domain\ValueObject\Phone;
use MarketPlace\Common\Domain\ValueObject\SendAt;
use MarketPlace\Common\Domain\ValueObject\UserStatus;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Common\Domain\ValueObject\VerifiedAt;
use MarketPlace\Common\Infrastructure\Service\Hydrator;
use MarketPlace\Market\Auth\Domain\Adapter\UserAdapterInterface;
use MarketPlace\Market\Auth\Domain\Entity\PhoneNumber;
use MarketPlace\Market\Auth\Domain\Entity\User;
use MarketPlace\Market\Auth\Domain\Entity\UserEmail;
use MarketPlace\Market\Auth\Domain\ValueObject\Token;
use MarketPlace\Market\Auth\Domain\ValueObject\UserId;
use MarketPlace\Market\Auth\Infrastructure\Exception\UserEmailNotFoundException;
use MarketPlace\Market\Auth\Infrastructure\Exception\UserPhoneNotFoundException;
use MarketPlace\Market\Auth\Infrastructure\Service\TokenService;
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
            'userId' => $userPhone->getUserId()?->getUserId()->getId(),
            'confirmationCode' => $userPhone->getConfirmationCode()?->getCode(),
            'sendAt' => $userPhone->getSendAt()?->toIsoFormat(),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function findUser(UserId $getUserId): User
    {
        $user = $this->api->findUser($getUserId->getUserId()->getId());

        return $this->hydrator->hydrate(User::class, [
            'id' => new Uuid($user['uuid']),
            'login' => new Login($user['login']),
            'personName' => new PersonName(
                firstName: $user['userName']['firstName'],
                lastName: $user['userName']['firstName'],
                middleName: $user['userName']['middleName']
            ),
            'phone' => $user['phone']
                ? $this->hydrator->hydrate(PhoneNumber::class, [
                    'uuid' => new Uuid($user['phone']['uuid']),
                    'phone' => Phone::fromString($user['phone']['phone']['regionIsoCode'], $user['phone']['phone']['number']),
                    'createdAt' => CreatedAt::fromIsoFormat($user['phone']['createdAt']),
                    'userId' => $user['phone']['userId'] ? new UserId(new Uuid($user['phone']['userId'])) : null,
                    'confirmationCode' => $user['phone']['confirmationCode'] ? new ConfirmationCode($user['phone']['confirmationCode']) : null,
                    'sendAt' => $user['phone']['sendAt'] ? SendAt::fromIsoFormat($user['phone']['sendAt']) : null,
                ])
                : null,
            'email' => $user['email']
                ? $this->hydrator->hydrate(UserEmail::class, [
                    'uuid' => new Uuid($user['email']['uuid']),
                    'email' => new Email($user['email']['email']),
                    'confirmationCode' => $user['email']['confirmationCode'] ? new ConfirmationCode($user['email']['confirmationCode']) : null,
                    'sendAt' => $user['email']['sendAt'] ? SendAt::fromIsoFormat($user['email']['sendAt']) : null,
                    'verifiedAt' => $user['email']['verifiedAt'] ? VerifiedAt::fromIsoFormat($user['email']['verifiedAt']) : null,
                    'userUuid' => new UserId(new Uuid($user['email']['userUuid'])),
                ])
                : null,
            'status' => new UserStatus($user['status']),
        ]);
    }

    /**
     * @throws UniqueTokenIdentifierConstraintViolationException
     * @throws OAuthServerException
     * @throws \JsonException
     */
    public function authorize(User $user): Token
    {
        return (new TokenService())->generate($user);
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

    /**
     * @throws UserEmailNotFoundException
     */
    public function findUserEmail(Email $email): UserEmail
    {
        $userEmail = $this->api->findUserEmail($email->getEmail());

        if (empty($userEmail)) {
            throw new UserEmailNotFoundException();
        }

        return $this->hydrator->hydrate(UserEmail::class, [
            'uuid' => new Uuid($userEmail['uuid']),
            'email' => new Email($userEmail['email']),
            'confirmationCode' => $userEmail['confirmationCode'] ? new ConfirmationCode($userEmail['confirmationCode']) : null,
            'sendAt' => $userEmail['sendAt'] ? SendAt::fromIsoFormat($userEmail['sendAt']) : null,
            'verifiedAt' => $userEmail['verifiedAt'] ? VerifiedAt::fromIsoFormat($userEmail['verifiedAt']) : null,
            'userUuid' => new UserId(new Uuid($userEmail['userUuid'])),
        ]);
    }

    public function updateUserEmail(UserEmail $userEmail): void
    {
        $this->api->updateUserEmail($userEmail->toArray());
    }
}
