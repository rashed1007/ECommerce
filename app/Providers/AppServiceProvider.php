<?php

namespace App\Providers;

use App\Http\Interfaces\AuthInterface;
use App\Http\Interfaces\OrderInterface;
use App\Http\Interfaces\ProductInterface;
use App\Http\Services\AuthService;
use App\Http\Services\OrderService;
use App\Http\Services\ProductService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(OrderInterface::class, OrderService::class);
        $this->app->bind(ProductInterface::class, ProductService::class);
        $this->app->bind(AuthInterface::class, AuthService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
