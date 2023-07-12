<?php

namespace MarketPlace\Partners\User\Domain\Repository;

use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Partners\User\Domain\Entity\User;
use MarketPlace\Partners\User\Domain\ValueObject\Email;
use MarketPlace\Partners\User\Domain\ValueObject\Phone;
use MarketPlace\Partners\User\Infrastructure\Exception\UserNotFoundException;
use MarketPlace\Partners\User\Infrastructure\Exception\UserUnauthenticatedException;

interface UserRepositoryInterface
{
    public function create(User $user): void;

    /**
     * @throws UserNotFoundException
     */
    public function update(User $user): void;

    /**
     * @throws UserNotFoundException
     */
    public function find(Uuid $uuid): User;

    /**
     * @throws UserUnauthenticatedException
     */
    public function me(): User;

    /**
     * @throws UserNotFoundException
     */
    public function findByPhone(Phone $phone): User;

    /**
     * @throws UserNotFoundException
     */
    public function findByEmail(Email $email): User;

    public function existByEmail(Email $email, ?Uuid $without = null): bool;

    public function existByPhone(Phone $phone, ?Uuid $without = null): bool;
}
