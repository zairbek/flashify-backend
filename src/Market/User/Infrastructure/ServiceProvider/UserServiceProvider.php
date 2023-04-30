<?php

declare(strict_types=1);

namespace MarketPlace\Market\User\Infrastructure\ServiceProvider;

use App;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MarketPlace\Market\User\Domain\Repository\UserPhoneRepositoryInterface;
use MarketPlace\Market\User\Domain\Service\UserPhoneService;
use MarketPlace\Market\User\Infrastructure\Repository\UserPhoneRepository;

class UserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserPhoneRepositoryInterface::class, UserPhoneRepository::class);
        $this->app->singleton(UserPhoneService::class, function (Application $app) {
            return new UserPhoneService(App::make(UserPhoneRepositoryInterface::class));
        });
    }
}
