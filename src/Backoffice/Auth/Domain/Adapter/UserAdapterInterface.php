<?php

namespace MarketPlace\Backoffice\Auth\Domain\Adapter;

use MarketPlace\Backoffice\Auth\Domain\Entity\User;
use MarketPlace\Backoffice\Auth\Infrastructure\Exception\UserCredentialsIncorrectException;
use MarketPlace\Backoffice\Auth\Infrastructure\Exception\UserNotFoundException;
use MarketPlace\Common\Domain\ValueObject\Email;
use MarketPlace\Common\Domain\ValueObject\Password;
use MarketPlace\Common\Domain\ValueObject\Uuid;

interface UserAdapterInterface
{
    /**
     * @param Uuid $uuid
     * @return User
     * @throws UserNotFoundException
     */
    public function findUser(Uuid $uuid): User;

    /**
     * @param Email $email
     * @param Password $password
     * @return User
     * @throws UserCredentialsIncorrectException
     */
    public function getByCredentials(Email $email, Password $password): User;
}
