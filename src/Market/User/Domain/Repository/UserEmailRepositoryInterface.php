<?php

namespace MarketPlace\Market\User\Domain\Repository;

use MarketPlace\Common\Domain\ValueObject\Email;
use MarketPlace\Market\User\Domain\Entity\UserEmail;
use MarketPlace\Market\User\Infrastructure\Exception\UserEmailNotFoundException;

interface UserEmailRepositoryInterface
{

    /**
     * @param Email $email
     * @return UserEmail
     * @throws UserEmailNotFoundException
     */
    public function findUserEmail(Email $email): UserEmail;

    public function update(UserEmail $userEmail): void;
}
