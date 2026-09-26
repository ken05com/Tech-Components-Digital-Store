<?php

namespace App\Catalog;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Read/write access to the catalog.
 *
 * The project runs without a database on purpose, so the 25 seed products from
 * CatalogData are the baseline and any Add / Edit / Delete performed from the
 * dashboard is persisted as a small JSON overlay in storage/app/catalog.
 * Deleting the overlay file restores the original document data.
 */
class ProductRepository
{
    public const OVERLAY = 'catalog/products.json';

    public const PER_PAGE = 10;

    /** In-request memo so a page never reads the overlay twice. */
    private ?array $overlay = null;

    /**
     * Seed catalog merged with the overlay, ordered by category then ID.
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
        return $this->all()
            ->sortByDesc(fn (Product $p) => $p->createdAt ?? '')
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

        $overlay = $this->readOverlay();
        $overlay['products'][] = $product->toArray();
        $this->writeOverlay($overlay);

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

        $overlay = $this->readOverlay();
        $products = $overlay['products'];

        foreach ($products as $index => $row) {
            if ((int) ($row['id'] ?? 0) === $product->id) {
                $products[$index] = $updated->toArray();
                break;
            }
        }

        $overlay['products'] = array_values($products);
        $this->writeOverlay($overlay);

        return $updated;
    }

    public function delete(Product $product): void
    {
        $overlay = $this->readOverlay();
        $overlay['products'] = array_values(array_filter(
            $overlay['products'],
            fn (array $row) => (int) ($row['id'] ?? 0) !== $product->id
        ));

        $this->writeOverlay($overlay);
    }

    /** Drops every local change and brings back the 25 rows from the document. */
    public function reset(): void
    {
        Storage::delete(self::OVERLAY);
        $this->overlay = null;
    }

    public function hasLocalChanges(): bool
    {
        return ($this->readOverlay()['products'] ?? []) !== [];
    }

    // ------------------------------------------------------------ internals --

    /** @return array<int, Product> */
    private function merge(): array
    {
        $seed = [];

        foreach (CatalogData::products() as $product) {
            $seed[$product->id] = $product;
        }

        foreach ($this->readOverlay()['products'] ?? [] as $row) {
            $product = Product::fromArray($row);
            $seed[$product->id] = $product;
        }

        return array_values($seed);
    }

    /** @return array{products: array<int, array<string, mixed>>} */
    private function readOverlay(): array
    {
        if ($this->overlay !== null) {
            return $this->overlay;
        }

        if (! Storage::exists(self::OVERLAY)) {
            return $this->overlay = ['products' => []];
        }

        $decoded = json_decode((string) Storage::get(self::OVERLAY), true);

        return $this->overlay = [
            'products' => is_array($decoded['products'] ?? null) ? $decoded['products'] : [],
        ];
    }

    /** @param  array{products: array<int, array<string, mixed>>}  $overlay */
    private function writeOverlay(array $overlay): void
    {
        Storage::put(self::OVERLAY, json_encode($overlay, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        $this->overlay = $overlay;
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
