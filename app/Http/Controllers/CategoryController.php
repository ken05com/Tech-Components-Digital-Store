<?php

namespace App\Http\Controllers;

use App\Catalog\CategoryRecord;
use App\Catalog\CategoryRepository;
use App\Catalog\ProductRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly CategoryRepository $categories,
    ) {}

    /**
     * The public category page. The slug is validated against the database so a
     * category added through management is instantly browsable.
     */
    public function show(Request $request, string $category): View|RedirectResponse
    {
        $record = $this->categories->findBySlug($category);

        if ($record === null) {
            abort(404, 'Unknown product category.');
        }

        $filters = [
            'search' => $request->query('search', ''),
            'sort' => $request->query('sort', 'default'),
            'category' => $record->value,
        ];

        $rows = $this->products->forCategory($record);
        $min = $rows->isEmpty() ? 0 : (int) $rows->min(fn ($product) => $product->priceMin);
        $max = $rows->isEmpty() ? 0 : (int) $rows->max(fn ($product) => $product->priceMax);

        return view('categories.show', [
            'category' => $record,
            'siblings' => $this->categories->all(),
            'products' => $this->products->paginate($filters),
            'filters' => $filters,
            'totalProducts' => $this->products->count(),
            'canManage' => Gate::allows('manage-products'),
            'canDelete' => Gate::allows('delete-products'),
            'summary' => [
                'products' => $rows->count(),
                'label' => 'Products',
                'entry' => $min,
                'exit' => $max,
            ],
            'pageTitle' => $record->label(),
        ]);
    }

    /**
     * The dashboard category page. Unlike the public storefront showcase this
     * keeps the management look: a summary, the catalog structure navigation and
     * the product table used to browse/curate a single category.
     */
    public function overview(Request $request, string $category): View
    {
        $record = $this->categories->findBySlug($category);

        if ($record === null) {
            abort(404, 'Unknown product category.');
        }

        $filters = [
            'search' => $request->query('search', ''),
            'sort' => $request->query('sort', 'default'),
            'category' => $record->value,
        ];

        $rows = $this->products->forCategory($record);
        $min = $rows->isEmpty() ? 0 : (int) $rows->min(fn ($product) => $product->priceMin);
        $max = $rows->isEmpty() ? 0 : (int) $rows->max(fn ($product) => $product->priceMax);

        return view('categories.overview', [
            'category' => $record,
            'siblings' => $this->categories->all(),
            'products' => $this->products->paginate($filters),
            'filters' => $filters,
            'totalProducts' => $this->products->count(),
            'canManage' => Gate::allows('manage-products'),
            'canDelete' => Gate::allows('delete-products'),
            'summary' => [
                'products' => $rows->count(),
                'label' => 'Products',
                'entry' => $min,
                'exit' => $max,
            ],
            'pageTitle' => $record->label(),
        ]);
    }

    // ------------------------------------------------------- management CRUD --

    public function index(): View
    {
        $categories = $this->categories->all();

        return view('categories.index', [
            'categories' => $categories,
            'counts' => $categories->mapWithKeys(
                fn (CategoryRecord $c) => [$c->id => $this->products->countByCategory($c)]
            ),
            'totalProducts' => $this->products->count(),
            'pageTitle' => 'Categories',
        ]);
    }

    public function create(): View
    {
        return view('categories.form', [
            'category' => null,
            'totalProducts' => $this->products->count(),
            'pageTitle' => 'Add Category',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateCategory($request);

        $this->categories->create($data);

        return redirect()
            ->route('categories.index')
            ->with('success_dialog', 'Category created');
    }

    public function edit(int $category): View
    {
        $record = $this->categories->find($category);

        if ($record === null) {
            abort(404, 'Category not found.');
        }

        return view('categories.form', [
            'category' => $record,
            'productCount' => $this->products->countByCategory($record),
            'totalProducts' => $this->products->count(),
            'pageTitle' => 'Edit '.$record->label(),
        ]);
    }

    public function update(Request $request, int $category): RedirectResponse
    {
        $record = $this->categories->find($category);

        if ($record === null) {
            abort(404, 'Category not found.');
        }

        $data = $this->validateCategory($request, $record);

        $this->categories->update($record, $data);

        return redirect()
            ->route('categories.index')
            ->with('success_dialog', 'Category updated');
    }

    public function destroy(int $category): RedirectResponse
    {
        $record = $this->categories->find($category);

        if ($record === null) {
            return redirect()
                ->route('categories.index')
                ->with('status', 'That category no longer exists.');
        }

        $count = $this->products->countByCategory($record);

        if ($count > 0) {
            return redirect()
                ->route('categories.index')
                ->withErrors(['category' => "“{$record->label()}” still has {$count} product(s). Move or delete them first."]);
        }

        $this->categories->delete($record);

        return redirect()
            ->route('categories.index')
            ->with('success_dialog', 'Category deleted');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateCategory(Request $request, ?CategoryRecord $category = null): array
    {
        $ignoreId = $category?->id;

        return $request->validate([
            'name' => [
                'required', 'string', 'min:2', 'max:120',
                Rule::unique('categories', 'name')->ignore($ignoreId),
            ],
            'short_label' => ['nullable', 'string', 'max:40'],
            'description' => ['nullable', 'string', 'max:255'],
            'monogram' => ['nullable', 'string', 'max:4'],
        ], [
            'name.required' => 'Please enter a category name.',
            'name.unique' => 'A category with that name already exists.',
        ]);
    }
}
