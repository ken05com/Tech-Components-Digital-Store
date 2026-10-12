<?php

namespace App\Catalog;

use Database\Seeders\CatalogSeeder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Read/write access to the catalog.
 *
 * The MySQL "products" table is the single source of truth at runtime. The 25
 * seed products from CatalogData are loaded into that table by CatalogSeeder,
 * and reset() restores the table to exactly those rows.
 */
class ProductRepository
{
    public const PER_PAGE = 12;

    public const LOW_STOCK_THRESHOLD = 5;

    public function __construct(private readonly CategoryRepository $categories) {}

    /**
     * All products, ordered by category position then ID.
     *
     * @return Collection<int, Product>
     */
    public function all(): Collection
    {
        return collect($this->merge())
            ->sortBy(fn (Product $product) => sprintf(
                '%03d-%03d',
                (int) $product->category->position,
                $product->categoryId
            ))
            ->values();
    }

    public function find(int $id): ?Product
    {
        return $this->all()->firstWhere('id', $id);
    }

    public function findByRouteKey(int|string $id): ?Product
    {
        return $this->find((int) $id);
    }

    public function count(): int
    {
        return $this->all()->count();
    }

    public function countByCategory(CategoryRecord|string $category): int
    {
        $name = $category instanceof CategoryRecord ? $category->value : $category;

        return $this->all()->filter(fn (Product $product) => $product->category->value === $name)->count();
    }

    /** @return Collection<int, CategoryRecord> */
    public function categories(): Collection
    {
        return $this->categories->all();
    }

    /** @return Collection<int, Product> */
    public function forCategory(CategoryRecord|string $category): Collection
    {
        $name = $category instanceof CategoryRecord ? $category->value : $category;

        return $this->all()->filter(fn (Product $product) => $product->category->value === $name)->values();
    }

    /**
     * Search / filter / sort in one place so every listing screen behaves the same.
     *
     * @param  array<string, mixed>  $filters
     * @return Collection<int, Product>
     */
    public function query(array $filters = []): Collection
    {
        $search = trim((string) ($filters['search'] ?? ''));
        $category = $filters['category'] ?? null;
        $sort = (string) ($filters['sort'] ?? 'default');

        $results = $this->all();

        if ($search !== '') {
            $needle = Str::lower($search);
            $results = $results->filter(function (Product $product) use ($needle) {
                return Str::contains(Str::lower($product->name), $needle)
                    || Str::contains(Str::lower($product->description), $needle)
                    || Str::contains(Str::lower($product->category->value), $needle);
            });
        }

        if (is_string($category) && $category !== '' && $category !== 'all') {
            $target = $this->categories->findBySlug($category) ?? $this->categories->findByName($category);
            $name = $target?->value ?? $category;
            $results = $results->filter(fn (Product $product) => $product->category->value === $name);
        }

        return match ($sort) {
            'name' => $results->sortBy(fn (Product $p) => Str::lower($p->name))->values(),
            'name-desc' => $results->sortByDesc(fn (Product $p) => Str::lower($p->name))->values(),
            'price-asc' => $results->sortBy(fn (Product $p) => [$p->priceMin, $p->priceMax])->values(),
            'price-desc' => $results->sortByDesc(fn (Product $p) => [$p->priceMin, $p->priceMax])->values(),
            default => $results->values(),
        };
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters = [], int $perPage = self::PER_PAGE): LengthAwarePaginator
    {
        $items = $this->query($filters);
        $page = max(1, (int) ($filters['page'] ?? request()->query('page', 1)));

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => $this->currentPath(), 'query' => $this->currentQuery($filters)]
        );
    }

    /**
     * Aggregate rows for the dashboard charts.
     *
     * @return array<int, array{category: string, slug: string, count: int, min: int, max: int, average: float}>
     */
    public function categoryStats(): array
    {
        return $this->all()
            ->groupBy(fn (Product $p) => $p->category->value)
            ->map(function (Collection $group, string $label) {
                $category = $group->first()->category;
                $mins = $group->map(fn (Product $p) => $p->priceMin);
                $maxes = $group->map(fn (Product $p) => $p->priceMax);

                return [
                    'category' => $label,
                    'slug' => $category->slug(),
                    'monogram' => $category->monogram(),
                    'count' => $group->count(),
                    'min' => (int) $mins->min(),
                    'max' => (int) $maxes->max(),
                    'average' => round($group->avg(fn (Product $p) => $p->averagePrice()), 2),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Cheapest and dearest rows in the catalog, for the price overview panel.
     *
     * @return array{cheapest: Product, dearest: Product, lowest: int, highest: int, average: float}
     */
    public function priceOverview(): array
    {
        $all = $this->all();
        $cheapest = $all->sortBy(fn (Product $p) => [$p->priceMin, $p->priceMax])->first();
        $dearest = $all->sortByDesc(fn (Product $p) => [$p->priceMax, $p->priceMin])->first();

        return [
            'cheapest' => $cheapest,
            'dearest' => $dearest,
            'lowest' => (int) $all->min(fn (Product $p) => $p->priceMin),
            'highest' => (int) $all->max(fn (Product $p) => $p->priceMax),
            'average' => round($all->avg(fn (Product $p) => $p->averagePrice()), 2),
        ];
    }

    /** @return Collection<int, Product> */
    public function recent(int $limit = 5): Collection
    {
        // No created_at column: fall back to id order. create() assigns
        // max(id) + 1, so the highest id is always the most recent row.
        return $this->all()
            ->sortByDesc(fn (Product $p) => $p->createdAt ?? sprintf('%06d', $p->id))
            ->take($limit)
            ->values();
    }

    // ------------------------------------------------------------ inventory --

    /** @return Collection<int, Product> */
    public function lowStock(int $threshold = self::LOW_STOCK_THRESHOLD, int $limit = 6): Collection
    {
        return $this->all()
            ->filter(fn (Product $p) => $p->stock <= $threshold)
            ->sortBy(fn (Product $p) => $p->stock)
            ->take($limit)
            ->values();
    }

    public function lowStockCount(int $threshold = self::LOW_STOCK_THRESHOLD): int
    {
        return $this->all()->filter(fn (Product $p) => $p->stock <= $threshold)->count();
    }

    public function outOfStockCount(): int
    {
        return $this->all()->where('stock', 0)->count();
    }

    public function totalStock(): int
    {
        return (int) $this->all()->sum(fn (Product $p) => $p->stock);
    }

    public function stockValue(): float
    {
        return (float) $this->all()->sum(fn (Product $p) => $p->stock * $p->averagePrice());
    }

    /**
     * Apply a stock movement and record it in the ledger.
     *
     * @param  int  $delta  Signed change (+receive / -sale).
     */
    public function adjustStock(Product $product, int $delta, string $type, ?string $reason = null, ?int $userId = null): Product
    {
        $balance = max(0, $product->stock + $delta);

        DB::table('products')->where('id', $product->id)->update([
            'stock' => $balance,
        ]);

        DB::table('inventory_movements')->insert([
            'product_id' => $product->id,
            'type' => $type,
            'quantity' => $type === 'out' ? -abs($delta) : abs($delta),
            'balance' => $balance,
            'reason' => $reason,
            'user_id' => $userId,
            'created_at' => now()->format('Y-m-d H:i:s'),
            'updated_at' => now()->format('Y-m-d H:i:s'),
        ]);

        return $this->find($product->id) ?? $product;
    }

    /** @return Collection<int, InventoryMovement> */
    public function movements(int $productId, int $limit = 20): Collection
    {
        return DB::table('inventory_movements')
            ->where('product_id', $productId)
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => new InventoryMovement(
                id: (int) $row->id,
                productId: (int) $row->product_id,
                type: (string) $row->type,
                quantity: (int) $row->quantity,
                balance: (int) $row->balance,
                reason: $row->reason,
                userId: $row->user_id !== null ? (int) $row->user_id : null,
                createdAt: $row->created_at,
            ))
            ->values();
    }

    // ---------------------------------------------------------------- CRUD --

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Product
    {
        $category = $this->resolveCategory((string) $data['category']);

        // Include soft-deleted rows when picking the next keys so an id or
        // per-category row number is never reused. The columns are VARCHAR, so
        // the max must be computed numerically rather than lexically.
        $nextId = ((int) DB::table('products')->selectRaw('MAX(CAST(id AS UNSIGNED)) as max_id')->value('max_id')) + 1;
        $nextCategoryId = ((int) DB::table('products')
            ->selectRaw('MAX(CAST(category_id AS UNSIGNED)) as max_cat')
            ->where('category', $category->value)
            ->value('max_cat')) + 1;
        $nextCategoryId = max(1, $nextCategoryId);
        $now = now()->format('Y-m-d H:i:s');

        $product = new Product(
            id: $nextId,
            categoryId: $nextCategoryId,
            category: $category,
            name: (string) $data['name'],
            description: (string) $data['description'],
            priceMin: (int) $data['price_min'],
            priceMax: (int) $data['price_max'],
            image: $data['image'] ?? null,
            stock: (int) ($data['stock'] ?? 0),
            variants: (array) ($data['variants'] ?? []),
            createdAt: $now,
            updatedAt: $now,
        );

        DB::table('products')->insert([
            'id' => $product->id,
            'category_id' => $product->categoryId,
            'category' => $product->category->value,
            'name' => $product->name,
            'description' => $product->description,
            'price_min' => $product->priceMin,
            'price_max' => $product->priceMax,
            'image' => $product->image,
            'stock' => $product->stock,
            'variants' => $product->variants === [] ? null : json_encode($product->variants),
            'deleted_at' => null,
        ]);

        return $product;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Product $product, array $data): Product
    {
        $category = $this->resolveCategory((string) $data['category']);

        $updated = new Product(
            id: $product->id,
            categoryId: (int) ($data['category_id'] ?? $product->categoryId),
            category: $category,
            name: (string) $data['name'],
            description: (string) $data['description'],
            priceMin: (int) $data['price_min'],
            priceMax: (int) $data['price_max'],
            image: ($data['image'] ?? null) ?: $product->image,
            stock: array_key_exists('stock', $data) ? (int) $data['stock'] : $product->stock,
            variants: array_key_exists('variants', $data) ? (array) $data['variants'] : $product->variants,
            createdAt: $product->createdAt,
            updatedAt: now()->format('Y-m-d H:i:s'),
        );

        DB::table('products')->where('id', $updated->id)->update([
            'category_id' => $updated->categoryId,
            'category' => $updated->category->value,
            'name' => $updated->name,
            'description' => $updated->description,
            'price_min' => $updated->priceMin,
            'price_max' => $updated->priceMax,
            'image' => $updated->image,
            'stock' => $updated->stock,
            'variants' => $updated->variants === [] ? null : json_encode($updated->variants),
        ]);

        return $updated;
    }

    /** Soft-deletes a product: the row and its image are kept for restoration. */
    public function delete(Product $product): void
    {
        DB::table('products')->where('id', $product->id)->update([
            'deleted_at' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Restores a soft-deleted product, optionally applying edits made in the
     * restore form. Clears deleted_at so it returns to the active catalog.
     *
     * @param  array<string, mixed>|null  $data
     */
    public function restore(Product $product, ?array $data = null): Product
    {
        if ($data !== null) {
            // update() writes the edited columns; deleted_at is handled below.
            $this->update($product, $data);
        }

        DB::table('products')->where('id', $product->id)->update(['deleted_at' => null]);

        return $this->find($product->id) ?? $product;
    }

    /** Permanently removes a product row. Callers must clean up its image. */
    public function forceDelete(Product $product): void
    {
        DB::table('products')->where('id', $product->id)->delete();
    }

    /** Products currently in the Deleted Items bin, newest first. */
    /** @return Collection<int, Product> */
    public function onlyTrashed(): Collection
    {
        return collect($this->merge(true))
            ->filter(fn (Product $product) => $product->isDeleted())
            ->sortByDesc(fn (Product $product) => $product->deletedAt)
            ->values();
    }

    public function findTrashed(int $id): ?Product
    {
        return $this->onlyTrashed()->firstWhere('id', $id);
    }

    public function countTrashed(): int
    {
        return DB::table('products')->whereNotNull('deleted_at')->count();
    }

    /**
     * Search within the deleted bin.
     *
     * @param  array<string, mixed>  $filters
     * @return Collection<int, Product>
     */
    public function trashedQuery(array $filters = []): Collection
    {
        $search = trim((string) ($filters['search'] ?? ''));
        $results = $this->onlyTrashed();

        if ($search !== '') {
            $needle = Str::lower($search);
            $results = $results->filter(fn (Product $product) => Str::contains(Str::lower($product->name), $needle)
                || Str::contains(Str::lower($product->description), $needle)
                || Str::contains(Str::lower($product->category->value), $needle));
        }

        return $results->values();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginateTrashed(array $filters = [], int $perPage = self::PER_PAGE): LengthAwarePaginator
    {
        $items = $this->trashedQuery($filters);
        $page = max(1, (int) ($filters['page'] ?? request()->query('page', 1)));

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            [
                'path' => 'deleted-items',
                'query' => array_filter(['search' => $filters['search'] ?? null], fn ($v) => $v !== null && $v !== ''),
            ]
        );
    }

    /** Wipes the table and re-seeds the 25 rows from CatalogData. */
    public function reset(): void
    {
        DB::table('products')->delete();
        Artisan::call('db:seed', ['--class' => CatalogSeeder::class, '--force' => true]);
    }

    public function hasLocalChanges(): bool
    {
        return DB::table('products')->count() !== count(CatalogData::products());
    }

    // ------------------------------------------------------------ internals --

    private function resolveCategory(string $name): CategoryRecord
    {
        return $this->categories->findByName($name)
            ?? $this->categories->findFirst()
            ?? CategoryRecord::fromName($name);
    }

    /** @return array<int, Product> */
    private function merge(bool $withTrashed = false): array
    {
        $query = DB::table('products');

        if (! $withTrashed) {
            $query->whereNull('deleted_at');
        }

        $map = $this->categories->all()->keyBy(fn (CategoryRecord $c) => $c->value)->all();

        return $query->get()
            ->map(fn ($row) => Product::fromArray((array) $row, $map))
            ->all();
    }

    private function currentPath(): string
    {
        return request()->path() === '/' ? 'dashboard' : request()->path();
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    private function currentQuery(array $filters): array
    {
        return array_filter([
            'search' => $filters['search'] ?? null,
            'category' => ($filters['category'] ?? null) !== 'all' ? ($filters['category'] ?? null) : null,
            'sort' => ($filters['sort'] ?? 'default') !== 'default' ? $filters['sort'] : null,
        ], fn ($value) => $value !== null && $value !== '');
    }
}
