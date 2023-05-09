<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\User\Infrastructure\ServiceProvider;

use App;
use Illuminate\Support\ServiceProvider;
use MarketPlace\Backoffice\User\Application\Service\UserService;
use MarketPlace\Backoffice\User\Domain\Repository\UserRepositoryInterface;
use MarketPlace\Backoffice\User\Infrastructure\Repository\UserRepository;

class UserServiceProvider extends ServiceProvider
{
    public array $bindings = [
        UserRepositoryInterface::class => UserRepository::class,
    ];

    public function register(): void
    {
        $this->app->singleton(UserService::class, function () {
            return new UserService(App::make(UserRepositoryInterface::class));
        });
    }
}
