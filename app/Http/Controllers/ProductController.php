<?php

namespace App\Http\Controllers;

use App\Catalog\Category;
use App\Catalog\CategoryRecord;
use App\Catalog\CategoryRepository;
use App\Catalog\Product;
use App\Catalog\ProductImageStore;
use App\Catalog\ProductRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly CategoryRepository $categories,
        private readonly ProductImageStore $images,
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->query('search', ''),
            'category' => $request->query('category', 'all'),
            'sort' => $request->query('sort', 'default'),
        ];

        $categories = $this->products->categories();

        return view('products.index', [
            'products' => $this->products->paginate($filters),
            'filters' => $filters,
            'categories' => $categories,
            'counts' => $categories->mapWithKeys(
                fn (CategoryRecord $c) => [$c->value => $this->products->countByCategory($c)]
            ),
            'totalProducts' => $this->products->count(),
            'canManage' => Gate::allows('manage-products'),
            'canDelete' => Gate::allows('delete-products'),
            'pageTitle' => 'All Products',
        ]);
    }

    public function inventoryIndex(Request $request): View
    {
        $filters = [
            'search' => $request->query('search', ''),
            'category' => $request->query('category', 'all'),
            'sort' => $request->query('sort', 'default'),
        ];

        return view('inventory.products', [
            'products' => $this->products->paginate($filters),
            'filters' => $filters,
            'categories' => $this->products->categories(),
            'canManage' => Gate::allows('manage-products'),
            'canDelete' => Gate::allows('delete-products'),
            'pageTitle' => 'Inventory Products',
        ]);
    }

    public function show(int $product): View
    {
        $item = $this->products->find($product);

        if ($item === null) {
            abort(404, 'Product not found.');
        }

        $related = $this->products->forCategory($item->category)
            ->reject(fn (Product $other) => $other->id === $item->id)
            ->values();

        return view('products.show', [
            'product' => $item,
            'related' => $related,
            'movements' => $this->products->movements($item->id, 12),
            'totalProducts' => $this->products->count(),
            'canManage' => Gate::allows('manage-products'),
            'canDelete' => Gate::allows('delete-products'),
            'pageTitle' => $item->name,
        ]);
    }

    /**
     * The public storefront product page (customer-facing, e-commerce layout).
     * The management detail page keeps living at products.show.
     */
    public function detail(int $product): View
    {
        $item = $this->products->find($product);

        if ($item === null) {
            abort(404, 'Product not found.');
        }

        $related = $this->products->forCategory($item->category)
            ->reject(fn (Product $other) => $other->id === $item->id)
            ->values();

        return view('storefront.product', [
            'product' => $item,
            'related' => $related,
            'pageTitle' => $item->name,
        ]);
    }

    public function create(Request $request): View
    {
        $category = $this->categories->findByName((string) $request->query('category'))
            ?? $this->categories->findFirst()
            ?? Category::ComputerComponents->toRecord();

        return view('products.create', [
            'product' => null,
            'categories' => $this->products->categories(),
            'selectedCategory' => $category,
            'totalProducts' => $this->products->count(),
            'pageTitle' => 'Add Product',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateProduct($request);
        $data['image'] = $this->images->store($request);

        $product = $this->products->create($data);

        // Record the opening stock so the ledger is complete from day one.
        if ((int) $data['stock'] > 0) {
            $this->products->adjustStock($product, (int) $data['stock'], 'in', 'Opening stock', $request->user()?->id);
        }

        return redirect()
            ->route('products.show', $product->id)
            ->with('success_dialog', 'Successfully Added New Collection');
    }

    public function edit(int $product): View
    {
        $item = $this->products->find($product);

        if ($item === null) {
            abort(404, 'Product not found.');
        }

        return view('products.edit', [
            'product' => $item,
            'categories' => $this->products->categories(),
            'selectedCategory' => $item->category,
            'totalProducts' => $this->products->count(),
            'canDelete' => Gate::allows('delete-products'),
            'pageTitle' => 'Edit Product',
        ]);
    }

    public function update(Request $request, int $product): RedirectResponse
    {
        $item = $this->products->find($product);

        if ($item === null) {
            abort(404, 'Product not found.');
        }

        $data = $this->validateProduct($request, $item);
        $data['stock'] = $item->stock;

        $uploaded = $this->images->store($request);

        if ($uploaded !== null) {
            $this->images->delete($item->image);
            $data['image'] = $uploaded;
        } else {
            $data['image'] = $item->image;
        }

        $updated = $this->products->update($item, $data);

        return redirect()
            ->route('products.show', $updated->id)
            ->with('success_dialog', 'Successfully Edited New Collection');
    }

    public function destroy(int $product): RedirectResponse
    {
        $item = $this->products->find($product);

        if ($item === null) {
            abort(404, 'Product not found.');
        }

        // Soft delete: the row and image stay so the product can be restored.
        $this->products->delete($item);

        return redirect()
            ->back(302, [], route('dashboard'))
            ->with('success_dialog', 'Collection moved to Deleted Items');
    }

    /** Adjust stock up or down and record the movement. */
    public function adjustStock(Request $request, int $product): RedirectResponse
    {
        $item = $this->products->find($product);

        if ($item === null) {
            abort(404, 'Product not found.');
        }

        $validated = $request->validate([
            'type' => ['required', Rule::in(['in', 'out', 'adjust'])],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'reason' => ['nullable', 'string', 'max:160'],
        ]);

        $type = $validated['type'];
        $quantity = (int) $validated['quantity'];
        $delta = $type === 'in' ? $quantity : -$quantity;

        $this->products->adjustStock($item, $delta, $type, $validated['reason'] ?? null, $request->user()?->id);

        $message = $type === 'in'
            ? 'Added '.$quantity.' to stock'
            : ($type === 'out' ? 'Removed '.$quantity.' from stock' : 'Stock adjusted');

        return redirect()
            ->route('products.show', $item->id)
            ->with('success_dialog', $message);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateProduct(Request $request, ?Product $product = null): array
    {
        $validated = $request->validate([
            'category' => ['required', Rule::in($this->categories->names())],
            'category_id' => ['nullable', 'integer', 'min:1', 'max:99'],
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'description' => ['required', 'string', 'min:10', 'max:255'],
            'price_min' => ['required', 'integer', 'min:0', 'max:1000000'],
            'price_max' => ['required', 'integer', 'min:0', 'max:1000000'],
            'stock' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'variants' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
        ], [
            'name.required' => 'Please enter a product name.',
            'description.min' => 'The description must be at least 10 characters.',
            'price_min.required' => 'Please enter a starting price.',
            'price_max.required' => 'Please enter a maximum price.',
        ]);

        $validated['price_min'] = (int) $validated['price_min'];
        $validated['price_max'] = (int) $validated['price_max'];
        $validated['stock'] = (int) ($validated['stock'] ?? 0);
        $validated['variants'] = $this->parseVariants($validated['variants'] ?? null);

        if ($validated['price_max'] < $validated['price_min']) {
            $request->validate([
                'price_max' => ['required', 'integer', 'gte:price_min'],
            ], [
                'price_max.gte' => 'The maximum price must be greater than or equal to the starting price.',
            ]);
        }

        unset($validated['image']);

        return $validated;
    }

    /**
     * Turn the variants textarea into a clean list. Each non-empty line is a
     * variant, optionally carrying a price after a pipe: "16GB DDR4 | 5500".
     *
     * @return array<int, array{name: string, price: int|null}>
     */
    private function parseVariants(?string $raw): array
    {
        if ($raw === null || trim($raw) === '') {
            return [];
        }

        $variants = [];

        foreach (preg_split('/\r\n|\r|\n/', $raw) ?: [] as $line) {
            $parts = array_map('trim', explode('|', trim($line), 2));
            $name = $parts[0] ?? '';

            if ($name === '') {
                continue;
            }

            $variants[] = [
                'name' => $name,
                'price' => isset($parts[1]) && is_numeric($parts[1]) ? (int) $parts[1] : null,
            ];
        }

        return $variants;
    }
}
