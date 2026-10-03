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
    public const PER_PAGE = 10;

    /**
     * All products, ordered by category then ID.
     *
     * @return Collection<int, Product>
     */
    public function all(): Collection
    {
        $merged = $this->merge();

        return collect($merged)
            ->sortBy(fn (Product $product) => sprintf(
                '%02d-%03d',
                (int) array_search($product->category, Category::cases(), true),
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

    public function countByCategory(Category $category): int
    {
        return $this->all()->where('category', $category)->count();
    }

    /** @return Collection<int, Category> */
    public function categories(): Collection
    {
        return collect(Category::ordered());
    }

    /** @return Collection<int, Product> */
    public function forCategory(Category $category): Collection
    {
        return $this->all()->where('category', $category)->values();
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
            $target = Category::tryFrom($category);
            $results = $target
                ? $results->where('category', $target)
                : $results->where('category.name', $category);
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

    // ---------------------------------------------------------------- CRUD --

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Product
    {
        $products = $this->all();
        $category = Category::from((string) $data['category']);

        $nextId = ($products->max('id') ?? 0) + 1;
        $nextCategoryId = ($products->where('category', $category)->max('categoryId') ?? 0) + 1;
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
        ]);

        return $product;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Product $product, array $data): Product
    {
        $category = Category::from((string) $data['category']);

        $updated = new Product(
            id: $product->id,
            categoryId: (int) ($data['category_id'] ?? $product->categoryId),
            category: $category,
            name: (string) $data['name'],
            description: (string) $data['description'],
            priceMin: (int) $data['price_min'],
            priceMax: (int) $data['price_max'],
            image: ($data['image'] ?? null) ?: $product->image,
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
        ]);

        return $updated;
    }

    public function delete(Product $product): void
    {
        DB::table('products')->where('id', $product->id)->delete();
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

    /** @return array<int, Product> */
    private function merge(): array
    {
        return DB::table('products')
            ->get()
            ->map(fn ($row) => Product::fromArray((array) $row))
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
