<?php

namespace MarketPlace\Market\Auth\Domain\Adapter;

use MarketPlace\Common\Domain\ValueObject\Phone;
use MarketPlace\Market\Auth\Domain\Entity\PhoneNumber;
use MarketPlace\Market\Auth\Domain\Entity\User;
use MarketPlace\Market\Auth\Domain\ValueObject\Token;
use MarketPlace\Market\Auth\Domain\ValueObject\UserId;
use MarketPlace\Market\Auth\Infrastructure\Exception\UserNotFoundException;
use MarketPlace\Market\Auth\Infrastructure\Exception\UserPhoneNotFoundException;

interface UserAdapterInterface
{
    public function existsUserByPhone(Phone $phone): bool;

    /**
     * @param Phone $phone
     * @return PhoneNumber
     * @throws UserPhoneNotFoundException
     */
    public function findUserPhone(Phone $phone): PhoneNumber;

    public function createUserPhone(PhoneNumber $phoneNumber): void;

    public function updateUserPhone(PhoneNumber $userPhone): void;

    /**
     * @param UserId $getUserId
     * @return User
     * @throws UserNotFoundException
     */
    public function findUser(UserId $getUserId): User;

    public function authorize(User $user): Token;

    public function createUserViaPhone(User $user): void;
}
