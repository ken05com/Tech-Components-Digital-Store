<?php

namespace App\Http\Controllers;

use App\Catalog\ProductRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SearchController extends Controller
{
    public function __construct(private readonly ProductRepository $products) {}

    public function __invoke(Request $request): View|RedirectResponse
    {
        $term = trim((string) ($request->query('q', $request->query('search', ''))));

        if ($term === '') {
            return redirect()->route('products.index');
        }

        $filters = [
            'search' => $term,
            'category' => $request->query('category', 'all'),
            'sort' => $request->query('sort', 'default'),
        ];

        $results = $this->products->query($filters);

        return view('search', [
            'term' => $term,
            'products' => $this->products->paginate($filters, 12),
            'filters' => $filters,
            'categories' => $this->products->categories(),
            'totalProducts' => $this->products->count(),
            'matchCount' => $results->count(),
            'canManage' => Gate::allows('manage-products'),
            'canDelete' => Gate::allows('delete-products'),
            'pageTitle' => 'Search',
        ]);
    }
}
