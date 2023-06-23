<?php

declare(strict_types=1);

namespace MarketPlace\Partners\Auth\Infrastructure\Adapter;

use Exception;
use LogicException;
use MarketPlace\Common\Domain\ValueObject\ConfirmationCode;
use MarketPlace\Common\Domain\ValueObject\CreatedAt;
use MarketPlace\Common\Domain\ValueObject\Login;
use MarketPlace\Common\Domain\ValueObject\PersonName;
use MarketPlace\Common\Domain\ValueObject\SendAt;
use MarketPlace\Common\Domain\ValueObject\UserStatus;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Common\Domain\ValueObject\VerifiedAt;
use MarketPlace\Common\Infrastructure\Service\Hydrator;
use MarketPlace\Market\Auth\Domain\Entity\UserEmail;
use MarketPlace\Market\Auth\Domain\ValueObject\UserId;
use MarketPlace\Market\Auth\Infrastructure\Exception\UserEmailNotFoundException;
use MarketPlace\Partners\Auth\Domain\ValueObject\Email;
use MarketPlace\Partners\Auth\Domain\ValueObject\Phone;
use MarketPlace\Partners\Auth\Domain\ValueObject\UserName;
use MarketPlace\Partners\Auth\Infrastructure\Exception\UserNotFoundException as AuthModuleUserNotFoundException;
use MarketPlace\Partners\Auth\Infrastructure\Exception\UserPhoneNotFoundException;
use MarketPlace\Partners\User\Infrastructure\Api\UserApi;
use MarketPlace\Partners\Auth\Domain\Adapter\UserAdapterInterface;
use MarketPlace\Partners\Auth\Domain\Entity\User;
use MarketPlace\Partners\User\Infrastructure\Exception\UserNotFoundException;

class UserAdapter implements UserAdapterInterface
{
    private UserApi $api;
    private Hydrator $hydrator;

    public function __construct()
    {
        $this->api = new UserApi();
        $this->hydrator = new Hydrator();
    }

    /**
     * @throws AuthModuleUserNotFoundException
     */
    public function update(User $user): void
    {
        try {
            $this->api->update([
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
                    'firstName' => $user->getUserName()?->getFirstName(),
                    'lastName' => $user->getUserName()?->getLastName(),
                ],
                'status' => $user->getStatus()->getStatus(),
            ]);
        } catch (UserNotFoundException $e) {
            throw new AuthModuleUserNotFoundException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @throws UserPhoneNotFoundException
     * @throws Exception
     */
    public function findByPhone(Phone $phone): User
    {
        $user = $this->api->findByPhone($phone->getRegionCode(), $phone->toString());

        if (empty($user)) {
            throw new UserPhoneNotFoundException();
        }

        return $this->hydrator($user);
    }

    /**
     * @throws AuthModuleUserNotFoundException
     */
    public function findByEmail(Email $email): User
    {
        $user = $this->api->findByEmail($email->getEmail());

        if (empty($user)) {
            throw new AuthModuleUserNotFoundException();
        }

        return $this->hydrator($user);
    }

    private function hydrator(array $user): User
    {
        return $this->hydrator->hydrate(User::class, [
            'uuid' => new Uuid($user['uuid']),
            'login' => new Login($user['login']),
            'email' => $user['email']
                ? new Email(
                    email: $user['email']['email'],
                    code: $user['email']['code'],
                    sendAt: $user['email']['sendAt'] ? SendAt::fromIsoFormat($user['email']['sendAt']) : null,
                )
                : null,
            'phone' => $user['phone']
                ? Phone::fromString(
                    regionCode: $user['phone']['regionIsoCode'],
                    phoneString:  $user['phone']['number'],
                    code: $user['phone']['code'],
                    sendAt: $user['phone']['sendAt'] ? SendAt::fromIsoFormat($user['phone']['sendAt']) : null,
                )
                : null,
            'userName' => new UserName(
                firstName: $user['userName']['firstName'],
                lastName: $user['userName']['lastName'],
            ),
            'status' => new UserStatus($user['status'])
        ]);
    }


//    public function createUserPhone(PhoneNumber $userPhone): void
//    {
//        $this->api->createUserPhone([
//            'uuid' => $userPhone->getUuid()->getId(),
//            'phone' => [
//                'regionIsoCode' => $userPhone->getPhone()->getRegionCode(),
//                'number' => $userPhone->getPhone()->toString()
//            ],
//            'createdAt' => $userPhone->getCreatedAt()->toIsoFormat(),
//            'userId' => $userPhone->getUserId()?->getUserId(),
//            'confirmationCode' => $userPhone->getConfirmationCode()?->getCode(),
//            'sendAt' => $userPhone->getSendAt()?->toIsoFormat(),
//        ]);
//    }
//
//    public function updateUserPhone(PhoneNumber $userPhone): void
//    {
//        $this->api->updateUserPhone([
//            'uuid' => $userPhone->getUuid()->getId(),
//            'phone' => [
//                'regionIsoCode' => $userPhone->getPhone()->getRegionCode(),
//                'number' => $userPhone->getPhone()->toString()
//            ],
//            'createdAt' => $userPhone->getCreatedAt()->toIsoFormat(),
//            'userId' => $userPhone->getUserId()?->getUserId()->getId(),
//            'confirmationCode' => $userPhone->getConfirmationCode()?->getCode(),
//            'sendAt' => $userPhone->getSendAt()?->toIsoFormat(),
//        ]);
//    }
//
//    /**
//     * @inheritDoc
//     */
//    public function findUser(UserId $getUserId): User
//    {
//        $user = $this->api->findUser($getUserId->getUserId()->getId());
//
//        return $this->hydrator->hydrate(User::class, [
//            'id' => new Uuid($user['uuid']),
//            'login' => new Login($user['login']),
//            'personName' => new PersonName(
//                firstName: $user['userName']['firstName'],
//                lastName: $user['userName']['firstName'],
//                middleName: $user['userName']['middleName']
//            ),
//            'phone' => $user['phone']
//                ? $this->hydrator->hydrate(PhoneNumber::class, [
//                    'uuid' => new Uuid($user['phone']['uuid']),
//                    'phone' => Phone::fromString($user['phone']['phone']['regionIsoCode'], $user['phone']['phone']['number']),
//                    'createdAt' => CreatedAt::fromIsoFormat($user['phone']['createdAt']),
//                    'userId' => $user['phone']['userId'] ? new UserId(new Uuid($user['phone']['userId'])) : null,
//                    'confirmationCode' => $user['phone']['confirmationCode'] ? new ConfirmationCode($user['phone']['confirmationCode']) : null,
//                    'sendAt' => $user['phone']['sendAt'] ? SendAt::fromIsoFormat($user['phone']['sendAt']) : null,
//                ])
//                : null,
//            'email' => $user['email']
//                ? $this->hydrator->hydrate(UserEmail::class, [
//                    'uuid' => new Uuid($user['email']['uuid']),
//                    'email' => new Email($user['email']['email']),
//                    'confirmationCode' => $user['email']['confirmationCode'] ? new ConfirmationCode($user['email']['confirmationCode']) : null,
//                    'sendAt' => $user['email']['sendAt'] ? SendAt::fromIsoFormat($user['email']['sendAt']) : null,
//                    'verifiedAt' => $user['email']['verifiedAt'] ? VerifiedAt::fromIsoFormat($user['email']['verifiedAt']) : null,
//                    'userUuid' => new UserId(new Uuid($user['email']['userUuid'])),
//                ])
//                : null,
//            'status' => new UserStatus($user['status']),
//        ]);
//    }
//
//    public function createUserViaPhone(User $user): void
//    {
//        if (is_null($user->getPhone())){
//            throw new LogicException('При создание пользователя через номер телефона произошла ошибка.');
//        }
//
//        $this->api->createUserViaPhone([
//            'uuid' => $user->getId()->getId(),
//            'login' => $user->getLogin()->getLogin(),
//            'phoneUuid' => $user->getPhone()->getUuid()->getId(),
//        ]);
//    }
//
//    /**
//     * @throws UserEmailNotFoundException
//     */
//    public function findUserEmail(Email $email): UserEmail
//    {
//        $userEmail = $this->api->findUserEmail($email->getEmail());
//
//        if (empty($userEmail)) {
//            throw new UserEmailNotFoundException();
//        }
//
//        return $this->hydrator->hydrate(UserEmail::class, [
//            'uuid' => new Uuid($userEmail['uuid']),
//            'email' => new Email($userEmail['email']),
//            'confirmationCode' => $userEmail['confirmationCode'] ? new ConfirmationCode($userEmail['confirmationCode']) : null,
//            'sendAt' => $userEmail['sendAt'] ? SendAt::fromIsoFormat($userEmail['sendAt']) : null,
//            'verifiedAt' => $userEmail['verifiedAt'] ? VerifiedAt::fromIsoFormat($userEmail['verifiedAt']) : null,
//            'userUuid' => new UserId(new Uuid($userEmail['userUuid'])),
//        ]);
//    }
//
//    public function updateUserEmail(UserEmail $userEmail): void
//    {
//        $this->api->updateUserEmail($userEmail->toArray());
//    }
}
