<?php

declare(strict_types=1);

namespace MarketPlace\Partners\Account\Infrastructure\ServiceProvider;

use App;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MarketPlace\Market\User\Application\Service\UserEmailService;
use MarketPlace\Market\User\Application\Service\UserPhoneService;
use MarketPlace\Market\User\Application\Service\UserService;
use MarketPlace\Market\User\Domain\Repository\UserEmailRepositoryInterface;
use MarketPlace\Market\User\Domain\Repository\UserPhoneRepositoryInterface;
use MarketPlace\Market\User\Domain\Repository\UserRepositoryInterface;
use MarketPlace\Market\User\Infrastructure\Repository\UserEmailRepository;
use MarketPlace\Market\User\Infrastructure\Repository\UserPhoneRepository;
use MarketPlace\Market\User\Infrastructure\Repository\UserRepository;

class PartnerServiceProvider extends ServiceProvider
{
    public array $bindings = [
        UserPhoneRepositoryInterface::class => UserPhoneRepository::class,
        UserRepositoryInterface::class => UserRepository::class,
        UserEmailRepositoryInterface::class => UserEmailRepository::class,
    ];

    public function register(): void
    {
        $this->app->singleton(UserPhoneService::class, function () {
            return new UserPhoneService(App::make(UserRepositoryInterface::class), App::make(UserPhoneRepositoryInterface::class));
        });

        $this->app->singleton(UserEmailService::class, function () {
            return new UserEmailService(App::make(UserRepositoryInterface::class), App::get(UserEmailRepositoryInterface::class));
        });

        $this->app->singleton(UserService::class, function () {
            return new UserService(App::make(UserRepositoryInterface::class));
        });
    }
}
