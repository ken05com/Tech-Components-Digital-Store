<?php

namespace App\Http\Controllers;

use App\Catalog\Category;
use App\Catalog\ProductRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(private readonly ProductRepository $products) {}

    /**
     * One page per subtopic. $slug is validated against the Category enum so
     * the five category routes can never drift away from the document.
     */
    public function show(Request $request, string $slug): View|RedirectResponse
    {
        $category = Category::fromSlug($slug);

        if ($category === null) {
            abort(404, 'Unknown product category.');
        }

        $filters = [
            'search' => $request->query('search', ''),
            'sort' => $request->query('sort', 'default'),
            'category' => $category->value,
        ];

        $rows = $this->products->forCategory($category);
        $min = (int) $rows->min(fn ($product) => $product->priceMin);
        $max = (int) $rows->max(fn ($product) => $product->priceMax);

        return view('categories.show', [
            'category' => $category,
            'products' => $this->products->paginate($filters),
            'filters' => $filters,
            'totalProducts' => $this->products->count(),
            'summary' => [
                'products' => $rows->count(),
                'label' => 'Products',
                'entry' => $min,
                'exit' => $max,
            ],
            'pageTitle' => $category->label(),
        ]);
    }
}
