<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Auth\Infrastructure\ServiceProvider;

use App;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MarketPlace\Backoffice\Auth\Application\Service\AuthorizeService;
use MarketPlace\Backoffice\Auth\Domain\Adapter\UserAdapterInterface;
use MarketPlace\Backoffice\Auth\Domain\Repository\TokenRepositoryInterface;
use MarketPlace\Backoffice\Auth\Infrastructure\Adapter\UserAdapter;
use MarketPlace\Backoffice\Auth\Infrastructure\Repository\TokenRepository;

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
