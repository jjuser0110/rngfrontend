<?php

namespace App\Providers;

use App\Catalog\Contracts\HomestayCatalog;
use App\Catalog\Contracts\TourCatalog;
use App\Catalog\Database\DatabaseHomestayCatalog;
use App\Catalog\Database\DatabaseTourCatalog;
use App\Support\SiteInfo;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Homestays and tours staff manage in rngcarrental (Homestays / Tours menu).
        // The old sample listings (App\Catalog\Sample, resources/data) are no longer used.
        $this->app->singleton(HomestayCatalog::class, DatabaseHomestayCatalog::class);
        $this->app->singleton(TourCatalog::class, DatabaseTourCatalog::class);

        // DOKU online payment (config/services.php "doku")
        $this->app->bind(\App\Payments\Doku\DokuClient::class, fn () => \App\Payments\Doku\DokuClient::fromConfig());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Company contact details for the top bar and footer
        View::composer(['layouts.navtop', 'layouts.footer'], fn($view) => $view->with('site', SiteInfo::get()));
    }
}
