<?php

namespace App\Sales;

use App\Catalog\Product;
use App\Catalog\ProductRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Query-builder access to completed sales, plus the checkout transaction that
 * writes a sale, its line items and the matching inventory movements together.
 */
class SaleRepository
{
    public function __construct(private readonly ProductRepository $products) {}

    /** @return Collection<int, Sale> */
    public function all(): Collection
    {
        return DB::table('sales')
            ->orderByDesc('id')
            ->get()
            ->map(fn ($row) => $this->hydrate((array) $row))
            ->values();
    }

    public function find(int $id): ?Sale
    {
        $row = DB::table('sales')->where('id', $id)->first();

        return $row ? $this->hydrate((array) $row, $this->items($id)) : null;
    }

    public function findByReference(string $reference): ?Sale
    {
        $row = DB::table('sales')->where('reference', $reference)->first();

        return $row ? $this->hydrate((array) $row, $this->items((int) $row->id)) : null;
    }

    /** @return Collection<int, SaleItem> */
    public function items(int $saleId): Collection
    {
        return DB::table('sale_items')
            ->where('sale_id', $saleId)
            ->orderBy('id')
            ->get()
            ->map(fn ($row) => new SaleItem(
                id: (int) $row->id,
                saleId: (int) $row->sale_id,
                productId: (int) $row->product_id,
                productName: (string) $row->product_name,
                unitPrice: (int) $row->unit_price,
                quantity: (int) $row->quantity,
                subtotal: (int) $row->subtotal,
            ))
            ->values();
    }

    /** @return Collection<int, Sale> */
    public function recent(int $limit = 5): Collection
    {
        return $this->all()->take($limit)->values();
    }

    public function count(): int
    {
        return DB::table('sales')->count();
    }

    public function itemsSold(): int
    {
        return (int) DB::table('sales')->sum('items_count');
    }

    public function revenue(): float
    {
        return (float) DB::table('sales')->sum('total');
    }

    public function revenueToday(): float
    {
        return (float) DB::table('sales')->whereDate('created_at', today())->sum('total');
    }

    /**
     * Persist a sale and apply its stock movements atomically.
     *
     * @param  array<int, array{product: Product, quantity: int}>  $lines
     */
    public function checkout(string $customerName, ?string $customerEmail, array $lines, ?int $userId = null): Sale
    {
        return DB::transaction(function () use ($customerName, $customerEmail, $lines, $userId) {
            $itemsCount = 0;
            $total = 0;

            $prepared = array_map(function (array $line) use (&$itemsCount, &$total) {
                /** @var Product $product */
                $product = $line['product'];
                $quantity = max(1, (int) $line['quantity']);
                $unitPrice = $product->priceMin;
                $subtotal = $unitPrice * $quantity;

                $itemsCount += $quantity;
                $total += $subtotal;

                return compact('product', 'quantity', 'unitPrice', 'subtotal');
            }, $lines);

            $now = now()->format('Y-m-d H:i:s');

            $saleId = DB::table('sales')->insertGetId([
                'reference' => $this->uniqueReference(),
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'items_count' => $itemsCount,
                'total' => $total,
                'user_id' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $sale = DB::table('sales')->where('id', $saleId)->first();

            foreach ($prepared as $line) {
                DB::table('sale_items')->insert([
                    'sale_id' => $saleId,
                    'product_id' => $line['product']->id,
                    'product_name' => $line['product']->name,
                    'unit_price' => $line['unitPrice'],
                    'quantity' => $line['quantity'],
                    'subtotal' => $line['subtotal'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $this->products->adjustStock(
                    $line['product'],
                    -$line['quantity'],
                    'out',
                    'Sold via '.$sale->reference,
                    $userId,
                );
            }

            return $this->find($saleId);
        });
    }

    /** @param array<string, mixed> $row */
    private function hydrate(array $row, ?Collection $items = null): Sale
    {
        return new Sale(
            id: (int) $row['id'],
            reference: (string) $row['reference'],
            customerName: (string) $row['customer_name'],
            customerEmail: $row['customer_email'] ?? null,
            itemsCount: (int) $row['items_count'],
            total: (int) $row['total'],
            userId: $row['user_id'] !== null ? (int) $row['user_id'] : null,
            createdAt: $row['created_at'] ?? null,
            items: $items ?? new Collection,
        );
    }

    private function uniqueReference(): string
    {
        do {
            $reference = 'TC-'.now()->format('Ymd').'-'.Str::upper(Str::random(5));
        } while (DB::table('sales')->where('reference', $reference)->exists());

        return $reference;
    }
}
