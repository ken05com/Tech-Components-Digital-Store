<?php

use App\Catalog\Category;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tech Components routes
|--------------------------------------------------------------------------
| The five category routes are registered from the Category enum so the URLs
| can never drift away from the subtopics in the source document.
*/

Route::get('/', fn () => redirect()->route('dashboard'))->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('search', SearchController::class)->name('search');

Route::get('settings', [PageController::class, 'settings'])->name('settings');
Route::get('profile', [PageController::class, 'profile'])->name('profile');
Route::post('catalog/reset', [PageController::class, 'resetCatalog'])->name('catalog.reset');

Route::get('products', [ProductController::class, 'index'])->name('products.index');
Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('products', [ProductController::class, 'store'])->name('products.store');
Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show')->whereNumber('product');
Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit')->whereNumber('product');
Route::match(['put', 'patch'], 'products/{product}', [ProductController::class, 'update'])->name('products.update')->whereNumber('product');
Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy')->whereNumber('product');

foreach (Category::ordered() as $category) {
    Route::get('categories/'.$category->slug(), [CategoryController::class, 'show'])
        ->defaults('category', $category->slug())
        ->name('categories.'.$category->slug());
}
