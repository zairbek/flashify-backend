<?php

declare(strict_types=1);

namespace MarketPlace\Partners\Account\Application\Service;

use MarketPlace\Common\Domain\Events\EventDispatcher;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Market\User\Domain\Entity\User;
use MarketPlace\Partners\Account\Domain\Entity\Account;
use MarketPlace\Partners\Account\Domain\Repository\AccountRepositoryInterface;
use MarketPlace\Partners\Account\Infrastructure\Exception\AccountNotFoundException;
use MarketPlace\Partners\Account\Infrastructure\Exception\AccountUnauthenticatedException;

class AccountService
{
    private array $listeners = [

    ];
    private EventDispatcher $eventDispatcher;
    private AccountRepositoryInterface $repository;

    public function __construct(AccountRepositoryInterface $repository)
    {
        $this->eventDispatcher = new EventDispatcher($this->listeners);
        $this->repository = $repository;
    }

    /**
     * @throws AccountNotFoundException
     */
    public function find(Uuid $uuid): User
    {
        return $this->repository->find($uuid);
    }

    /**
     * @throws AccountUnauthenticatedException
     */
    public function me(): Account
    {
        return $this->repository->me();
    }
}
