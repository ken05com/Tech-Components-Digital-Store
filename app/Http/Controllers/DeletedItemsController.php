<?php

namespace App\Http\Controllers;

use App\Catalog\CategoryRepository;
use App\Catalog\ProductImageStore;
use App\Catalog\ProductRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeletedItemsController extends Controller
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly CategoryRepository $categories,
        private readonly ProductImageStore $images,
    ) {}

    public function index(Request $request): View
    {
        $filters = ['search' => $request->query('search', '')];

        return view('deleted-items.index', [
            'products' => $this->products->paginateTrashed($filters),
            'filters' => $filters,
            'deletedCount' => $this->products->countTrashed(),
            'totalProducts' => $this->products->count(),
            'pageTitle' => 'Deleted Items',
        ]);
    }

    public function restoreForm(int $id): View
    {
        $product = $this->products->findTrashed($id);

        if ($product === null) {
            abort(404, 'Deleted product not found.');
        }

        return view('deleted-items.restore', [
            'product' => $product,
            'categories' => $this->products->categories(),
            'selectedCategory' => $product->category,
            'totalProducts' => $this->products->count(),
            'pageTitle' => 'Restore Product',
        ]);
    }

    public function restore(Request $request, int $id): RedirectResponse
    {
        $product = $this->products->findTrashed($id);

        if ($product === null) {
            abort(404, 'Deleted product not found.');
        }

        $data = $this->validateProduct($request);
        $data['image'] = $this->images->store($request) ?: $product->image;

        $this->products->restore($product, $data);

        return redirect()
            ->route('deleted-items.index')
            ->with('success_dialog', 'Successfully Restored Collection');
    }

    public function destroy(int $id): RedirectResponse
    {
        $product = $this->products->findTrashed($id);

        if ($product === null) {
            return redirect()
                ->route('deleted-items.index')
                ->with('status', 'That product is no longer in the Deleted Items bin.');
        }

        $this->products->forceDelete($product);
        $this->images->delete($product->image);

        return redirect()
            ->route('deleted-items.index')
            ->with('success_dialog', 'Collection permanently deleted');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateProduct(Request $request): array
    {
        $validated = $request->validate([
            'category' => ['required', Rule::in($this->categories->names())],
            'category_id' => ['nullable', 'integer', 'min:1', 'max:99'],
            'name' => ['required', 'string', 'min:2', 'max:100'],
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
}
