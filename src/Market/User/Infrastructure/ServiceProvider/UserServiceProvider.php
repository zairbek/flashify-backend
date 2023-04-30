<?php

declare(strict_types=1);

namespace MarketPlace\Market\User\Infrastructure\ServiceProvider;

use App;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MarketPlace\Market\User\Application\Service\UserPhoneService;
use MarketPlace\Market\User\Application\Service\UserService;
use MarketPlace\Market\User\Domain\Repository\UserPhoneRepositoryInterface;
use MarketPlace\Market\User\Domain\Repository\UserRepositoryInterface;
use MarketPlace\Market\User\Infrastructure\Repository\UserPhoneRepository;
use MarketPlace\Market\User\Infrastructure\Repository\UserRepository;

class UserServiceProvider extends ServiceProvider
{
    public array $bindings = [
        UserPhoneRepositoryInterface::class => UserPhoneRepository::class,
        UserRepositoryInterface::class => UserRepository::class,
    ];

    public function register(): void
    {
        $this->app->singleton(UserPhoneService::class, function (Application $app) {
            return new UserPhoneService(App::make(UserRepositoryInterface::class), App::make(UserPhoneRepositoryInterface::class));
        });

        $this->app->singleton(UserService::class, function () {
            return new UserService(App::make(UserRepositoryInterface::class));
        });
    }
}
