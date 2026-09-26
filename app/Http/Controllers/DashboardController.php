<?php

namespace App\Http\Controllers;

use App\Catalog\Category;
use App\Catalog\ProductRepository;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly ProductRepository $products) {}

    public function index(): View
    {
        $overview = $this->products->priceOverview();

        return view('dashboard', [
            'totalProducts' => $this->products->count(),
            'categories' => $this->products->categories(),
            'counts' => collect(Category::cases())
                ->mapWithKeys(fn (Category $category) => [$category->value => $this->products->countByCategory($category)]),
            'categoryStats' => $this->products->categoryStats(),
            'recent' => $this->products->recent(6),
            'overview' => $overview,
            'pageTitle' => 'Dashboard',
        ]);
    }
}
