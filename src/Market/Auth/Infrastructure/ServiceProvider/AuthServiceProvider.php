<?php

declare(strict_types=1);

namespace MarketPlace\Market\Auth\Infrastructure\ServiceProvider;

use App;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MarketPlace\Market\Auth\Domain\Adapter\UserAdapterInterface;
use MarketPlace\Market\Auth\Domain\Service\AuthorizeService;
use MarketPlace\Market\Auth\Infrastructure\Adapter\UserAdapter;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserAdapterInterface::class, UserAdapter::class);
        $this->app->singleton(AuthorizeService::class, function (Application $app) {
            return new AuthorizeService(App::make(UserAdapterInterface::class));
        });
    }
}
