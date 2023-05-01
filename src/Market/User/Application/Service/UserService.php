<?php

declare(strict_types=1);

namespace MarketPlace\Market\User\Application\Service;

use MarketPlace\Common\Domain\Events\EventDispatcher;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Market\User\Domain\Entity\User;
use MarketPlace\Market\User\Domain\Repository\UserRepositoryInterface;
use MarketPlace\Market\User\Infrastructure\Exception\UserNotFoundException;

class UserService
{
    private EventDispatcher $eventDispatcher;

    private array $listeners = [

    ];
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
}
