<?php

declare(strict_types=1);

namespace MarketPlace\Market\Category\Infrastructure\ServiceProvider;

use App;
use Illuminate\Support\ServiceProvider;
use MarketPlace\Market\Category\Application\Service\CategoryService;
use MarketPlace\Market\Category\Domain\Repository\CategoryRepositoryInterface;
use MarketPlace\Market\Category\Infrastructure\Repository\CategoryRepository;

class CategoryServiceProvider extends ServiceProvider
{
    public array $bindings = [
        CategoryRepositoryInterface::class => CategoryRepository::class
    ];

    public function register(): void
    {
        $this->app->singleton(CategoryService::class, function () {
            return new CategoryService(App::make(CategoryRepositoryInterface::class));
        });
    }
}
