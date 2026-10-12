<?php

namespace App\Http\Controllers;

use App\Cart\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cart) {}

    public function index(): View
    {
        return view('cart.index', [
            'lines' => $this->cart->lines(),
            'subtotal' => $this->cart->subtotal(),
            'pageTitle' => 'Your Cart',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:100'],
            'variant' => ['nullable', 'string', 'max:120'],
        ]);

        $this->cart->add(
            (int) $validated['product_id'],
            (int) ($validated['quantity'] ?? 1),
            $validated['variant'] ?? null,
        );

        if ($request->input('redirect') === 'checkout') {
            return redirect()->route('checkout.create');
        }

        return redirect()
            ->back()
            ->with('success_dialog', 'Added to cart');
    }

    public function update(Request $request, int $product): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:100'],
            'variant' => ['nullable', 'string', 'max:120'],
        ]);

        $this->cart->update($product, (int) $validated['quantity'], $validated['variant'] ?? null);

        return redirect()
            ->route('cart.index')
            ->with('success_dialog', 'Cart updated');
    }

    public function destroy(Request $request, int $product): RedirectResponse
    {
        $variant = $request->input('variant');

        $this->cart->remove($product, is_string($variant) ? $variant : null);

        return redirect()
            ->route('cart.index')
            ->with('success_dialog', 'Item removed');
    }

    public function clear(): RedirectResponse
    {
        $this->cart->clear();

        return redirect()
            ->route('cart.index')
            ->with('success_dialog', 'Cart cleared');
    }
}
