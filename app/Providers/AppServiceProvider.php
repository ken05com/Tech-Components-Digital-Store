<?php

namespace App\Providers;

use App\Auth\User;
use App\Auth\UserProvider;
use App\Auth\UserRepository;
use App\Cart\CartService;
use App\Catalog\CategoryRepository;
use App\Catalog\ProductRepository;
use App\Sales\SaleRepository;
use App\Settings\SettingRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The catalog has no Eloquent layer, so a single shared repository
     * instance is reused for the whole request.
     */
    public function register(): void
    {
        $this->app->singleton(ProductRepository::class);
        $this->app->singleton(CategoryRepository::class);
        $this->app->singleton(UserRepository::class);
        $this->app->singleton(SettingRepository::class);
        $this->app->singleton(SaleRepository::class);
        $this->app->singleton(CartService::class);
    }

    public function boot(): void
    {
        // Query-builder user provider ("query" driver in config/auth.php).
        Auth::provider('query', fn ($app, array $config) => new UserProvider($app->make(UserRepository::class)));

        // One gate per ability, all resolved through the Role enum. This keeps
        // authorization centralized and independent of the UI.
        $abilities = [
            'view-dashboard',
            'view-catalog',
            'manage-products',
            'delete-products',
            'view-deleted',
            'restore-products',
            'force-delete',
            'manage-settings',
            'reset-catalog',
            'manage-users',
            'manage-categories',
            'view-inventory',
            'view-sales',
        ];

        foreach ($abilities as $ability) {
            Gate::define($ability, fn (User $user) => $user->can($ability));
        }
    }
}
