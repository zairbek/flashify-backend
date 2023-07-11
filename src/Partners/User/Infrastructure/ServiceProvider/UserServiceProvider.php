<?php

declare(strict_types=1);

namespace MarketPlace\Partners\User\Infrastructure\ServiceProvider;

use App;
use Illuminate\Support\ServiceProvider;
use MarketPlace\Partners\User\Application\Service\UserService;
use MarketPlace\Partners\User\Domain\Repository\RequestCodeRepositoryInterface;
use MarketPlace\Partners\User\Domain\Repository\UserRepositoryInterface;
use MarketPlace\Partners\User\Infrastructure\Repository\RequestCodeRepository;
use MarketPlace\Partners\User\Infrastructure\Repository\UserRepository;

class UserServiceProvider extends ServiceProvider
{
    public array $bindings = [
        UserRepositoryInterface::class => UserRepository::class,
        RequestCodeRepositoryInterface::class => RequestCodeRepository::class,
    ];

    public function register(): void
    {
        $this->app->singleton(UserService::class, function () {
            return new UserService(
                App::make(UserRepositoryInterface::class),
                App::make(RequestCodeRepositoryInterface::class)
            );
        });
    }
}
