<?php

declare(strict_types=1);

namespace MarketPlace\Partners\User\Application\Service;

use MarketPlace\Common\Domain\Events\EventDispatcher;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Partners\User\Domain\Entity\User;
use MarketPlace\Partners\User\Domain\Repository\UserRepositoryInterface;
use MarketPlace\Partners\User\Infrastructure\Exception\UserNotFoundException;
use MarketPlace\Partners\User\Infrastructure\Exception\UserUnauthenticatedException;

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
     * @throws UserUnauthenticatedException
     */
    public function me(): Account
    {
        return $this->repository->me();
    }
}
