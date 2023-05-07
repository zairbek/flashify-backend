<?php

declare(strict_types=1);

namespace MarketPlace\Market\Auth\Infrastructure\ServiceProvider;

use App;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MarketPlace\Market\Auth\Application\Service\AuthorizeService;
use MarketPlace\Market\Auth\Domain\Adapter\UserAdapterInterface;
use MarketPlace\Market\Auth\Domain\Repository\TokenRepositoryInterface;
use MarketPlace\Market\Auth\Infrastructure\Adapter\UserAdapter;
use MarketPlace\Market\Auth\Infrastructure\Repository\TokenRepository;

class AuthServiceProvider extends ServiceProvider
{
    public array $bindings = [
        UserAdapterInterface::class => UserAdapter::class,
        TokenRepositoryInterface::class => TokenRepository::class,
    ];

    public function register(): void
    {
        $this->app->singleton(AuthorizeService::class, function (Application $app) {
            return new AuthorizeService(
                userAdapter: App::make(UserAdapterInterface::class),
                tokenRepository: App::make(TokenRepositoryInterface::class)
            );
        });
    }
}
