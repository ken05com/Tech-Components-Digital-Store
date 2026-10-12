<?php

namespace App\Http\Controllers;

use App\Cart\CartService;
use App\Sales\SaleRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly SaleRepository $sales,
    ) {}

    public function create(): View|RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Your cart is empty.');
        }

        return view('checkout.create', [
            'lines' => $this->cart->lines(),
            'subtotal' => $this->cart->subtotal(),
            'pageTitle' => 'Checkout',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Your cart is empty.');
        }

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'min:2', 'max:120'],
            'customer_email' => ['nullable', 'string', 'email', 'max:160'],
        ]);

        $lines = array_map(
            fn (array $line) => ['product' => $line['product'], 'quantity' => $line['quantity']],
            $this->cart->lines()
        );

        // Guard against a line exceeding the stock left at checkout time.
        foreach ($this->cart->lines() as $line) {
            if ($line['quantity'] > $line['product']->stock) {
                return redirect()
                    ->route('cart.index')
                    ->withErrors(['cart' => "Only {$line['product']->stock} × {$line['product']->name} left in stock."]);
            }
        }

        $sale = $this->sales->checkout(
            $validated['customer_name'],
            $validated['customer_email'] ?? null,
            $lines,
            $request->user()?->id,
        );

        $this->cart->clear();

        return redirect()
            ->route('orders.show', $sale->reference)
            ->with('success_dialog', 'Order placed');
    }

    public function confirmation(string $reference): View
    {
        $sale = $this->sales->findByReference($reference);

        if ($sale === null) {
            abort(404, 'Order not found.');
        }

        return view('checkout.confirmation', [
            'sale' => $sale,
            'pageTitle' => 'Order '.$sale->reference,
        ]);
    }
}
