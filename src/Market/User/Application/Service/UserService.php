<?php

declare(strict_types=1);

namespace MarketPlace\Market\User\Application\Service;

use MarketPlace\Common\Domain\Events\EventDispatcher;
use MarketPlace\Market\User\Domain\Repository\UserRepositoryInterface;

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
}
