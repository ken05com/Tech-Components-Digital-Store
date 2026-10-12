<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeletedItemsController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tech Components routes
|--------------------------------------------------------------------------
| The storefront (home page + read-only catalog browsing) is public so a
| visitor can open / and shop the catalog without an account. Management
| screens (dashboard, settings, CRUD, deleted items) stay behind the auth
| middleware; actions are further guarded by the ability gates defined in
| AppServiceProvider. Categories are database-backed now, so the public
| category URL resolves dynamically rather than from a fixed list.
*/

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');
});

// --- Public storefront -----------------------------------------------------

Route::get('/', [StorefrontController::class, 'index'])->name('home');

Route::get('today', [StorefrontController::class, 'today'])->name('store.today');
Route::get('following', [StorefrontController::class, 'following'])->name('store.following');
Route::get('ideas', [StorefrontController::class, 'ideas'])->name('ideas');
Route::get('ideas/{slug}', [StorefrontController::class, 'idea'])->name('ideas.show');

Route::get('search', SearchController::class)->name('search');

Route::get('products', [ProductController::class, 'index'])->name('products.index');
Route::get('products/{product}', [ProductController::class, 'show'])
    ->whereNumber('product')->name('products.show');

Route::get('product/{product}', [ProductController::class, 'detail'])
    ->whereNumber('product')->name('products.detail');

Route::get('categories/{category}', [CategoryController::class, 'show'])
    ->where('category', '[A-Za-z0-9\-]+')->name('categories.show');

// --- Cart & checkout (public) ----------------------------------------------

Route::get('cart', [CartController::class, 'index'])->name('cart.index');
Route::post('cart', [CartController::class, 'store'])->name('cart.store');
Route::patch('cart/{product}', [CartController::class, 'update'])->whereNumber('product')->name('cart.update');
Route::delete('cart/{product}', [CartController::class, 'destroy'])->whereNumber('product')->name('cart.destroy');
Route::delete('cart', [CartController::class, 'clear'])->name('cart.clear');

Route::get('checkout', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('orders/{reference}', [CheckoutController::class, 'confirmation'])->name('orders.show');

// --- Authenticated management ----------------------------------------------

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('about', [PageController::class, 'about'])->name('about');

    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('settings', [PageController::class, 'settings'])->name('settings');
    Route::put('settings', [PageController::class, 'updateSettings'])
        ->middleware('can:manage-settings')->name('settings.update');
    Route::get('profile', [PageController::class, 'profile'])->name('profile');
    Route::post('catalog/reset', [PageController::class, 'resetCatalog'])
        ->middleware('can:reset-catalog')->name('catalog.reset');

    Route::get('products/create', [ProductController::class, 'create'])
        ->middleware('can:manage-products')->name('products.create');
    Route::post('products', [ProductController::class, 'store'])
        ->middleware('can:manage-products')->name('products.store');
    Route::get('products/{product}/edit', [ProductController::class, 'edit'])
        ->whereNumber('product')->middleware('can:manage-products')->name('products.edit');
    Route::match(['put', 'patch'], 'products/{product}', [ProductController::class, 'update'])
        ->whereNumber('product')->middleware('can:manage-products')->name('products.update');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])
        ->whereNumber('product')->middleware('can:delete-products')->name('products.destroy');
    Route::post('products/{product}/stock', [ProductController::class, 'adjustStock'])
        ->whereNumber('product')->middleware('can:manage-products')->name('products.stock');

    // --- Inventory ---------------------------------------------------------
    Route::get('inventory', [InventoryController::class, 'index'])
        ->middleware('can:view-inventory')->name('inventory.index');
    Route::get('inventory/products', [ProductController::class, 'inventoryIndex'])
        ->middleware('can:view-inventory')->name('inventory.products');

    // --- Sales & User Account Pages ----------------------------------------
    Route::get('sales', [SaleController::class, 'index'])
        ->middleware('can:view-sales')->name('sales.index');
    Route::get('purchases', [SaleController::class, 'purchases'])->name('purchases.index');
    Route::get('notifications', [PageController::class, 'notifications'])->name('notifications.index');
    Route::get('vouchers', [PageController::class, 'vouchers'])->name('vouchers.index');
    Route::get('sales/{sale}', [SaleController::class, 'show'])
        ->whereNumber('sale')->name('sales.show');

    // --- Users & roles -----------------------------------------------------
    Route::get('manage/users', [UserController::class, 'index'])
        ->middleware('can:manage-users')->name('users.index');
    Route::get('manage/users/create', [UserController::class, 'create'])
        ->middleware('can:manage-users')->name('users.create');
    Route::post('manage/users', [UserController::class, 'store'])
        ->middleware('can:manage-users')->name('users.store');
    Route::get('manage/users/{user}/edit', [UserController::class, 'edit'])
        ->whereNumber('user')->middleware('can:manage-users')->name('users.edit');
    Route::match(['put', 'patch'], 'manage/users/{user}', [UserController::class, 'update'])
        ->whereNumber('user')->middleware('can:manage-users')->name('users.update');
    Route::delete('manage/users/{user}', [UserController::class, 'destroy'])
        ->whereNumber('user')->middleware('can:manage-users')->name('users.destroy');

    // --- Category management ----------------------------------------------
    Route::get('manage/categories/{category}/products', [CategoryController::class, 'overview'])
        ->where('category', '[A-Za-z0-9\-]+')->name('categories.overview');
    Route::get('manage/categories', [CategoryController::class, 'index'])
        ->middleware('can:manage-categories')->name('categories.index');
    Route::get('manage/categories/create', [CategoryController::class, 'create'])
        ->middleware('can:manage-categories')->name('categories.create');
    Route::post('manage/categories', [CategoryController::class, 'store'])
        ->middleware('can:manage-categories')->name('categories.store');
    Route::get('manage/categories/{category}/edit', [CategoryController::class, 'edit'])
        ->whereNumber('category')->middleware('can:manage-categories')->name('categories.edit');
    Route::match(['put', 'patch'], 'manage/categories/{category}', [CategoryController::class, 'update'])
        ->whereNumber('category')->middleware('can:manage-categories')->name('categories.update');
    Route::delete('manage/categories/{category}', [CategoryController::class, 'destroy'])
        ->whereNumber('category')->middleware('can:manage-categories')->name('categories.destroy');

    // --- Deleted items -----------------------------------------------------
    Route::get('deleted-items', [DeletedItemsController::class, 'index'])
        ->middleware('can:view-deleted')->name('deleted-items.index');
    Route::get('deleted-items/{id}/restore', [DeletedItemsController::class, 'restoreForm'])
        ->whereNumber('id')->middleware('can:restore-products')->name('deleted-items.restore.form');
    Route::patch('deleted-items/{id}/restore', [DeletedItemsController::class, 'restore'])
        ->whereNumber('id')->middleware('can:restore-products')->name('deleted-items.restore');
    Route::delete('deleted-items/{id}/permanent', [DeletedItemsController::class, 'destroy'])
        ->whereNumber('id')->middleware('can:force-delete')->name('deleted-items.destroy');
});
