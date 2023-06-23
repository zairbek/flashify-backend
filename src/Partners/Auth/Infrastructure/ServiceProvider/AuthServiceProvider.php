<?php

declare(strict_types=1);

namespace MarketPlace\Partners\Auth\Infrastructure\ServiceProvider;

use App;
use Illuminate\Support\ServiceProvider;
use MarketPlace\Partners\Auth\Application\Service\AuthorizeService;
use MarketPlace\Partners\Auth\Domain\Adapter\UserAdapterInterface;
use MarketPlace\Partners\Auth\Domain\Repository\TokenRepositoryInterface;
use MarketPlace\Partners\Auth\Infrastructure\Adapter\UserAdapter;
use MarketPlace\Partners\Auth\Infrastructure\Repository\TokenRepository;

class AuthServiceProvider extends ServiceProvider
{
    public array $bindings = [
        UserAdapterInterface::class => UserAdapter::class,
        TokenRepositoryInterface::class => TokenRepository::class,
    ];

    public function register(): void
    {
        $this->app->singleton(AuthorizeService::class, function () {
            return new AuthorizeService(
                userAdapter: App::make(UserAdapterInterface::class),
                tokenRepository: App::make(TokenRepositoryInterface::class)
            );
        });
    }
}
