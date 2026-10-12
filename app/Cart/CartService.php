<?php

namespace App\Cart;

use App\Catalog\Product;
use App\Catalog\ProductRepository;

/**
 * Session-backed shopping cart.
 *
 * The cart is intentionally not stored in the database: it is a per-visitor
 * scratch pad that only becomes a sale at checkout. A line is identified by its
 * product plus the chosen variant, so the same product can sit in the cart more
 * than once with different options. Every line is clamped to available stock.
 */
class CartService
{
    private const SESSION_KEY = 'cart';

    public function __construct(private readonly ProductRepository $products) {}

    /**
     * Normalised cart contents.
     *
     * @return array<string, array{product_id: int, variant: string|null, quantity: int}>
     */
    public function raw(): array
    {
        $cart = session(self::SESSION_KEY, []);

        if (! is_array($cart)) {
            return [];
        }

        $normalised = [];

        foreach ($cart as $key => $value) {
            if (is_array($value)) {
                $normalised[(string) $key] = [
                    'product_id' => (int) ($value['product_id'] ?? 0),
                    'variant' => $this->normaliseVariant($value['variant'] ?? null),
                    'quantity' => (int) ($value['quantity'] ?? 0),
                ];

                continue;
            }

            // Legacy shape (product id => quantity) from before variants existed.
            $normalised[(string) $key] = [
                'product_id' => (int) $key,
                'variant' => null,
                'quantity' => (int) $value,
            ];
        }

        return $normalised;
    }

    /**
     * @return array<int, array{key: string, product: Product, variant: string|null, quantity: int, unit_price: int, subtotal: int}>
     */
    public function lines(): array
    {
        $lines = [];

        foreach ($this->raw() as $key => $item) {
            $product = $this->products->find($item['product_id']);

            if ($product === null) {
                continue;
            }

            $quantity = (int) $item['quantity'];
            $unitPrice = $this->unitPrice($product, $item['variant']);

            $lines[] = [
                'key' => $key,
                'product' => $product,
                'variant' => $item['variant'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $unitPrice * $quantity,
            ];
        }

        return $lines;
    }

    public function add(int $productId, int $quantity = 1, ?string $variant = null): void
    {
        $product = $this->products->find($productId);

        if ($product === null || $product->stock <= 0) {
            return;
        }

        $variant = $this->normaliseVariant($variant);
        $cart = $this->raw();
        $key = $this->key($productId, $variant);
        $quantity = max(1, $quantity);
        $existing = (int) ($cart[$key]['quantity'] ?? 0);

        $cart[$key] = [
            'product_id' => $productId,
            'variant' => $variant,
            'quantity' => min($product->stock, $existing + $quantity),
        ];

        $this->write($cart);
    }

    public function update(int $productId, int $quantity, ?string $variant = null): void
    {
        $variant = $this->normaliseVariant($variant);
        $key = $this->key($productId, $variant);
        $cart = $this->raw();

        $product = $this->products->find($productId);

        if ($quantity <= 0 || $product === null) {
            unset($cart[$key]);
            $this->write($cart);

            return;
        }

        $cart[$key] = [
            'product_id' => $productId,
            'variant' => $variant,
            'quantity' => min($product->stock, $quantity),
        ];

        $this->write($cart);
    }

    public function remove(int $productId, ?string $variant = null): void
    {
        $cart = $this->raw();
        unset($cart[$this->key($productId, $this->normaliseVariant($variant))]);
        $this->write($cart);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /** Number of distinct lines in the cart. */
    public function lineCount(): int
    {
        return count($this->raw());
    }

    /** Total quantity across every line (used by the header badge). */
    public function count(): int
    {
        return (int) array_sum(array_map(
            fn (array $item) => $item['quantity'],
            $this->raw()
        ));
    }

    public function subtotal(): int
    {
        return (int) array_sum(array_map(fn (array $line) => $line['subtotal'], $this->lines()));
    }

    public function formattedSubtotal(): string
    {
        return '₱'.number_format($this->subtotal());
    }

    /**
     * The price used for a line: the variant's own price when it has one,
     * otherwise the product's starting price.
     */
    public function unitPrice(Product $product, ?string $variant = null): int
    {
        if ($variant !== null) {
            $price = $product->variantPrice($variant);

            if ($price !== null) {
                return $price;
            }
        }

        return $product->priceMin;
    }

    private function key(int $productId, ?string $variant): string
    {
        return $variant === null ? (string) $productId : $productId.'::'.$variant;
    }

    private function normaliseVariant(mixed $variant): ?string
    {
        if (! is_string($variant)) {
            return null;
        }

        $variant = trim($variant);

        return $variant === '' ? null : $variant;
    }

    /** @param array<string, array{product_id: int, variant: string|null, quantity: int}> $cart */
    private function write(array $cart): void
    {
        session([self::SESSION_KEY => $cart]);
    }
}
