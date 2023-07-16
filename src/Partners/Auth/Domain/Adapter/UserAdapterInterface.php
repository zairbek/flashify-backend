<?php

namespace MarketPlace\Partners\Auth\Domain\Adapter;

use MarketPlace\Common\Domain\ValueObject\ConfirmationCode;
use MarketPlace\Partners\Auth\Domain\Entity\User;
use MarketPlace\Partners\Auth\Domain\ValueObject\Email;
use MarketPlace\Partners\Auth\Domain\ValueObject\Phone;
use MarketPlace\Partners\Auth\Infrastructure\Exception\UserNotFoundException;
use MarketPlace\Partners\Auth\Infrastructure\Exception\UserPhoneNotFoundException;
use MarketPlace\Partners\User\Infrastructure\Exception\RequestCodeThrottlingException;

interface UserAdapterInterface
{
    public function create(User $user): void;
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

    /**
     * @throws RequestCodeThrottlingException
     */
    public function requestCodeForRegister(Phone $phone): void;

    public function isConfirmationCodeCorrect(Phone $phone, ConfirmationCode $code): bool;
}
