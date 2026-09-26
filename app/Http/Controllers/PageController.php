<?php

namespace App\Http\Controllers;

use App\Catalog\Category;
use App\Catalog\ProductRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PageController extends Controller
{
    public function __construct(private readonly ProductRepository $products) {}

    public function settings(): View
    {
        return view('settings', [
            'categories' => $this->products->categories(),
            'counts' => collect(Category::cases())
                ->mapWithKeys(fn (Category $c) => [$c->value => $this->products->countByCategory($c)]),
            'totalProducts' => $this->products->count(),
            'hasLocalChanges' => $this->products->hasLocalChanges(),
            'pageTitle' => 'Settings',
        ]);
    }

    public function profile(): View
    {
        return view('profile', [
            'totalProducts' => $this->products->count(),
            'categoryCount' => count(Category::cases()),
            'pageTitle' => 'Profile',
        ]);
    }

    /** Restores the 25 rows exactly as they appear in the source document. */
    public function resetCatalog(): RedirectResponse
    {
        $this->products->reset();

        return redirect()
            ->route('settings')
            ->with('status', 'Catalog restored to the original 25 products from the document.');
    }
}
