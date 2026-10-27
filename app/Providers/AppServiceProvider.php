<?php

namespace App\Providers;

use App\Catalog\Contracts\HomestayCatalog;
use App\Catalog\Contracts\TourCatalog;
use App\Catalog\Sample\SampleHomestayCatalog;
use App\Catalog\Sample\SampleTourCatalog;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Homestays and tours are sample data for now. To manage them in rngcarrental,
        // bind Eloquent versions of these contracts here instead.
        $this->app->singleton(HomestayCatalog::class, SampleHomestayCatalog::class);
        $this->app->singleton(TourCatalog::class, SampleTourCatalog::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
