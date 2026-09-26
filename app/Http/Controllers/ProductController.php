<?php

namespace App\Http\Controllers;

use App\Catalog\Category;
use App\Catalog\Product;
use App\Catalog\ProductRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function __construct(private readonly ProductRepository $products) {}

    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->query('search', ''),
            'category' => $request->query('category', 'all'),
            'sort' => $request->query('sort', 'default'),
        ];

        return view('products.index', [
            'products' => $this->products->paginate($filters),
            'filters' => $filters,
            'categories' => $this->products->categories(),
            'counts' => collect(Category::ordered())
                ->mapWithKeys(fn (Category $c) => [$c->value => $this->products->countByCategory($c)]),
            'totalProducts' => $this->products->count(),
            'pageTitle' => 'All Products',
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
            'totalProducts' => $this->products->count(),
            'pageTitle' => $item->name,
        ]);
    }

    public function create(Request $request): View
    {
        $category = Category::tryFrom((string) $request->query('category')) ?? Category::ComputerComponents;

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

        $data['image'] = $this->storeImage($request);

        $product = $this->products->create($data);

        return redirect()
            ->route('products.show', $product->id)
            ->with('status', "{$product->name} was added to the catalog.");
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

        $uploaded = $this->storeImage($request);
        $data['image'] = $uploaded ?: $item->image;

        $updated = $this->products->update($item, $data);

        return redirect()
            ->route('products.show', $updated->id)
            ->with('status', "{$updated->name} was updated.");
    }

    public function destroy(int $product): RedirectResponse
    {
        $item = $this->products->find($product);

        if ($item === null) {
            abort(404, 'Product not found.');
        }

        $this->products->delete($item);

        return redirect()
            ->route('dashboard')
            ->with('status', "{$item->name} was removed from the catalog.");
    }

    /**
     * @return array<string, mixed>
     */
    private function validateProduct(Request $request, ?Product $product = null): array
    {
        $validated = $request->validate([
            'category' => ['required', Rule::in(Category::labels())],
            'category_id' => ['nullable', 'integer', 'min:1', 'max:99'],
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'description' => ['required', 'string', 'min:10', 'max:255'],
            'price_min' => ['required', 'integer', 'min:0', 'max:1000000'],
            'price_max' => ['required', 'integer', 'min:0', 'max:1000000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
        ], [
            'name.required' => 'Please enter a product name.',
            'description.min' => 'The description must be at least 10 characters.',
            'price_min.required' => 'Please enter a starting price.',
            'price_max.required' => 'Please enter a maximum price.',
        ]);

        $validated['price_min'] = (int) $validated['price_min'];
        $validated['price_max'] = (int) $validated['price_max'];

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

    private function storeImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        $file = $request->file('image');
        $name = Str::slug(pathinfo((string) $request->input('name'), PATHINFO_FILENAME)) ?: 'product';
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = $name.'-'.Str::lower(Str::random(8)).'.'.$extension;
        $path = 'images/products/'.$filename;

        Storage::disk('public')->putFileAs('images/products', $file, $filename);

        // Uploads live on the "public" disk, so hand back a root-relative path
        // (the 25 seed images sit directly in public/images/products instead).
        return '/'.ltrim(Storage::disk('public')->url($path), '/');
    }
}
