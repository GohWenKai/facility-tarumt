<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Repositories\Interfaces\BookingRepositoryInterface;
use App\Repositories\BookingRepository;
use App\Models\Asset;
use App\Observers\AssetObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->bind(BookingRepositoryInterface::class, BookingRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
        \App\Models\Asset::observe(\App\Observers\AssetObserver::class);
        
    }
    
}
