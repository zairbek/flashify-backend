<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Digest\Icon\Infrastructure\ServiceProvider;

use App;
use Illuminate\Support\ServiceProvider;
use MarketPlace\Backoffice\Digest\Icon\Application\Service\IconService;
use MarketPlace\Backoffice\Digest\Icon\Domain\Repository\IconRepositoryInterface;
use MarketPlace\Backoffice\Digest\Icon\Infrastructure\Repository\IconRepository;

class IconServiceProvider extends ServiceProvider
{
    public array $bindings = [
        IconRepositoryInterface::class => IconRepository::class,
    ];

    public function register(): void
    {
        $this->app->singleton(IconService::class, function () {
            return new IconService(App::make(IconRepositoryInterface::class));
        });
    }
}
