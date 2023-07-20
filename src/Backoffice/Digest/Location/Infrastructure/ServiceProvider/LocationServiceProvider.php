<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Digest\Location\Infrastructure\ServiceProvider;

use App;
use Illuminate\Support\ServiceProvider;
use MarketPlace\Backoffice\Digest\Location\Application\Service\LocationService;
use MarketPlace\Backoffice\Digest\Location\Domain\Repository\LocationRepositoryInterface;
use MarketPlace\Backoffice\Digest\Location\Infrastructure\Repository\LocationRepository;

class LocationServiceProvider extends ServiceProvider
{
    public array $bindings = [
        LocationRepositoryInterface::class => LocationRepository::class,
    ];

    public function register(): void
    {
        $this->app->singleton(LocationService::class, function () {
            return new LocationService(App::make(LocationRepositoryInterface::class));
        });
    }
}
