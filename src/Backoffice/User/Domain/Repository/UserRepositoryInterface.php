<?php

namespace MarketPlace\Backoffice\User\Domain\Repository;

use MarketPlace\Backoffice\User\Domain\Entity\User;
use MarketPlace\Backoffice\User\Infrastructure\Exception\UserCredentialsIncorrectException;
use MarketPlace\Backoffice\User\Infrastructure\Exception\UserNotFoundException;
use MarketPlace\Backoffice\User\Infrastructure\Exception\UserUnauthenticatedException;
use MarketPlace\Common\Domain\ValueObject\Email;
use MarketPlace\Common\Domain\ValueObject\Password;
use MarketPlace\Common\Domain\ValueObject\Uuid;

interface UserRepositoryInterface
{
    public function create(User $user): void;

    /**
     * @param Uuid $uuid
     * @return User
     * @throws UserNotFoundException
     */
    public function find(Uuid $uuid): User;

    /**
     * @return User
     * @throws UserUnauthenticatedException
     */
    public function me(): User;


    /**
     * @param Email $email
     * @param Password $password
     * @return User
     * @throws UserCredentialsIncorrectException
     */
    public function getByCredentials(Email $email, Password $password): User;
}
