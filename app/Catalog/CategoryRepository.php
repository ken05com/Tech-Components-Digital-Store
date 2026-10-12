<?php

namespace App\Catalog;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Query-builder access to the "categories" table. No Eloquent, matching the
 * rest of the catalog layer.
 */
class CategoryRepository
{
    /**
     * Active categories in display order.
     *
     * @return Collection<int, CategoryRecord>
     */
    public function all(): Collection
    {
        return DB::table('categories')
            ->orderBy('position')
            ->orderBy('name')
            ->get()
            ->map(fn ($row) => $this->hydrate((array) $row))
            ->values();
    }

    /** @return Collection<int, CategoryRecord> */
    public function ordered(): Collection
    {
        return $this->all();
    }

    public function find(int $id): ?CategoryRecord
    {
        $row = DB::table('categories')->where('id', $id)->first();

        return $row ? $this->hydrate((array) $row) : null;
    }

    public function findBySlug(string $slug): ?CategoryRecord
    {
        $row = DB::table('categories')->where('slug', Str::slug($slug))->first();

        return $row ? $this->hydrate((array) $row) : null;
    }

    public function findByName(string $name): ?CategoryRecord
    {
        $row = DB::table('categories')->where('name', $name)->first();

        return $row ? $this->hydrate((array) $row) : null;
    }

    public function findFirst(): ?CategoryRecord
    {
        return $this->all()->first();
    }

    public function count(): int
    {
        return DB::table('categories')->count();
    }

    /** @return array<int, string> */
    public function names(): array
    {
        return $this->all()->pluck('value')->all();
    }

    /**
     * Resolve a category name or id, used when a product stores its category as
     * a plain string.
     */
    public function resolve(string|int $key): ?CategoryRecord
    {
        if (is_int($key) || ctype_digit((string) $key)) {
            return $this->find((int) $key);
        }

        return $this->findByName((string) $key) ?? $this->findBySlug((string) $key);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): CategoryRecord
    {
        $name = trim((string) $data['name']);
        $now = now()->format('Y-m-d H:i:s');
        $nextPosition = ((int) DB::table('categories')->max('position')) + 1;

        $id = DB::table('categories')->insertGetId([
            'slug' => $this->uniqueSlug((string) ($data['slug'] ?? $name)),
            'name' => $name,
            'short_label' => (string) ($data['short_label'] ?? Str::limit($name, 18, '')),
            'description' => (string) ($data['description'] ?? ''),
            'monogram' => (string) ($data['monogram'] ?? $this->monogramFor($name)),
            'position' => (int) ($data['position'] ?? $nextPosition),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $this->find($id);
    }

    /**
     * Update a category. When the name changes the products that referenced the
     * old name are re-pointed so nothing is left orphaned.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(CategoryRecord $category, array $data): CategoryRecord
    {
        $name = trim((string) $data['name']);
        $now = now()->format('Y-m-d H:i:s');

        DB::table('categories')->where('id', $category->id)->update([
            'slug' => $this->uniqueSlug((string) ($data['slug'] ?? $name), $category->id),
            'name' => $name,
            'short_label' => (string) ($data['short_label'] ?? Str::limit($name, 18, '')),
            'description' => (string) ($data['description'] ?? ''),
            'monogram' => (string) ($data['monogram'] ?? $this->monogramFor($name)),
            'position' => (int) ($data['position'] ?? $category->position),
            'updated_at' => $now,
        ]);

        if ($name !== $category->value) {
            DB::table('products')->where('category', $category->value)->update(['category' => $name]);
        }

        return $this->find($category->id);
    }

    public function delete(CategoryRecord $category): void
    {
        DB::table('categories')->where('id', $category->id)->delete();
    }

    /** @param array<string, mixed> $row */
    private function hydrate(array $row): CategoryRecord
    {
        return new CategoryRecord(
            id: (int) $row['id'],
            slug: (string) $row['slug'],
            value: (string) $row['name'],
            short: (string) $row['short_label'],
            description: (string) $row['description'],
            monogram: (string) $row['monogram'],
            position: (int) $row['position'],
            createdAt: $row['created_at'] ?? null,
            updatedAt: $row['updated_at'] ?? null,
        );
    }

    private function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source) ?: 'category';
        $slug = $base;
        $suffix = 2;

        while (
            DB::table('categories')
                ->where('slug', $slug)
                ->when($ignoreId !== null, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    private function monogramFor(string $name): string
    {
        $words = preg_split('/[\s&\/]+/', trim($name)) ?: [];
        $letters = array_map(
            fn (string $word) => Str::upper(Str::substr($word, 0, 1)),
            array_slice(array_filter($words), 0, 2)
        );

        return $letters === [] ? '??' : implode('', $letters);
    }
}
