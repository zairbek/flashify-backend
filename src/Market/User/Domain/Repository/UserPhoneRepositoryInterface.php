<?php

namespace MarketPlace\Market\User\Domain\Repository;

use MarketPlace\Common\Domain\ValueObject\Phone;
use MarketPlace\Market\User\Domain\Entity\UserPhoneNumber;
use MarketPlace\Market\User\Infrastructure\Exception\UserPhoneNumberNotFoundException;

interface UserPhoneRepositoryInterface
{
    /**
     * @param Phone $phone
     * @return UserPhoneNumber
     * @throws UserPhoneNumberNotFoundException
     */
    public function findUserPhone(Phone $phone): UserPhoneNumber;

    public function update(UserPhoneNumber $userPhoneNumber): void;
}
