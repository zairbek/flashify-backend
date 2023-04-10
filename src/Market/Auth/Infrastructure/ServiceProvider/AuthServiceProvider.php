<?php

declare(strict_types=1);

namespace MarketPlace\Market\Auth\Infrastructure\ServiceProvider;

use Illuminate\Support\ServiceProvider;
use MarketPlace\Market\Auth\Domain\Adapter\UserAdapterInterface;

class AuthServiceProvider extends ServiceProvider
{

    public function register(): void
    {
//        $this->app->bind(UserAdapterInterface::class, );
    }


}
