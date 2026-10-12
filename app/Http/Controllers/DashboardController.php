<?php

namespace App\Http\Controllers;

use App\Catalog\CategoryRecord;
use App\Catalog\ProductRepository;
use App\Settings\SettingRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly SettingRepository $settings,
    ) {}

    public function index(): View
    {
        $overview = $this->products->priceOverview();
        $categories = $this->products->categories();

        return view('dashboard', [
            'totalProducts' => $this->products->count(),
            'categories' => $categories,
            'counts' => $categories->mapWithKeys(
                fn (CategoryRecord $category) => [$category->value => $this->products->countByCategory($category)]
            ),
            'categoryStats' => $this->products->categoryStats(),
            'products' => $this->products->paginate([], 5),
            'overview' => $overview,
            'lowStock' => $this->products->lowStock(),
            'lowStockCount' => $this->products->lowStockCount(),
            'outOfStockCount' => $this->products->outOfStockCount(),
            'totalStock' => $this->products->totalStock(),
            'author' => $this->settings->author(),
            'reflections' => $this->settings->reflections(),
            'deletedCount' => Gate::allows('view-deleted') ? $this->products->countTrashed() : null,
            'canManage' => Gate::allows('manage-products'),
            'pageTitle' => 'Dashboard',
        ]);
    }
}
