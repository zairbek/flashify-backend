<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\User\Application\Service;

use MarketPlace\Backoffice\Auth\Application\Dto\GetByCredentialsDto;
use MarketPlace\Backoffice\User\Domain\Entity\User;
use MarketPlace\Backoffice\User\Domain\Repository\UserRepositoryInterface;
use MarketPlace\Backoffice\User\Infrastructure\Exception\UserCredentialsIncorrectException;
use MarketPlace\Backoffice\User\Infrastructure\Exception\UserNotFoundException;
use MarketPlace\Backoffice\User\Infrastructure\Exception\UserUnauthenticatedException;
use MarketPlace\Common\Domain\Events\EventDispatcher;
use MarketPlace\Common\Domain\ValueObject\Email;
use MarketPlace\Common\Domain\ValueObject\Password;
use MarketPlace\Common\Domain\ValueObject\Uuid;

class UserService
{
    private array $listeners = [

    ];
    private EventDispatcher $eventDispatcher;
    private UserRepositoryInterface $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->eventDispatcher = new EventDispatcher($this->listeners);
        $this->repository = $repository;
    }

    /**
     * @throws UserNotFoundException
     */
    public function find(Uuid $uuid): User
    {
        return $this->repository->find($uuid);
    }

    /**
     * @throws UserCredentialsIncorrectException
     */
    public function getByCredentials(GetByCredentialsDto $dto): User
    {
        $email = new Email($dto->email);
        $password = new Password($dto->password);

        return $this->repository->getByCredentials($email, $password);
    }

    /**
     * @throws UserUnauthenticatedException
     */
    public function me(): User
    {
        return $this->repository->me();
    }
}
