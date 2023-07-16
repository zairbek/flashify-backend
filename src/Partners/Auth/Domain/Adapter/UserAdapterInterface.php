<?php

namespace MarketPlace\Partners\Auth\Domain\Adapter;

use MarketPlace\Market\Auth\Domain\Entity\PhoneNumber;
use MarketPlace\Market\Auth\Domain\Entity\UserEmail;
use MarketPlace\Market\Auth\Domain\ValueObject\UserId;
use MarketPlace\Market\Auth\Infrastructure\Exception\UserEmailNotFoundException;
use MarketPlace\Partners\Auth\Domain\Entity\User;
use MarketPlace\Partners\Auth\Domain\ValueObject\Email;
use MarketPlace\Partners\Auth\Domain\ValueObject\Phone;
use MarketPlace\Partners\Auth\Infrastructure\Exception\UserNotFoundException;
use MarketPlace\Partners\Auth\Infrastructure\Exception\UserPhoneNotFoundException;
use MarketPlace\Partners\User\Infrastructure\Exception\RequestCodeThrottlingException;

interface UserAdapterInterface
{
    /**
     * @throws UserNotFoundException
     */
    public function update(User $user): void;

    /**
     * @throws UserPhoneNotFoundException
     */
    public function findByPhone(Phone $phone): User;

    /**
     * @throws UserNotFoundException
     */
    public function findByEmail(Email $email): User;

//    public function createUserPhone(PhoneNumber $phoneNumber): void;
//
//
//    /**
//     * @param UserId $getUserId
//     * @return User
//     * @throws UserNotFoundException
//     */
//    public function findUser(UserId $getUserId): User;
//
//    public function createUserViaPhone(User $user): void;
//
//    /**
//     * @param Email $email
//     * @return UserEmail
//     * @throws UserEmailNotFoundException
//     */
//    public function findUserEmail(Email $email): UserEmail;
//
//    public function updateUserEmail(UserEmail $userEmail): void;

    /**
     * @throws RequestCodeThrottlingException
     */
    public function requestCodeForRegister(Phone $phone): void;
}
