<?php

namespace App\Providers;

use App\Catalog\ProductRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The catalog has no database behind it, so a single shared repository
     * instance keeps the JSON overlay memoised for the whole request.
     */
    public function register(): void
    {
        $this->app->singleton(ProductRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
