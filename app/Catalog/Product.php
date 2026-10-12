<?php

namespace App\Catalog;

use Illuminate\Support\Str;

/**
 * A single catalog row. This is a plain value object on purpose: the project
 * intentionally ships without a database, so nothing here extends Eloquent.
 */
final class Product implements \JsonSerializable
{
    public CategoryRecord $category;

    /**
     * $category accepts either a Category enum case (CatalogData still speaks in
     * enum cases) or a database-backed CategoryRecord; it is normalised to a
     * record so the rest of the app only ever deals with one type.
     */
    public function __construct(
        public int $id,
        public int $categoryId,
        Category|CategoryRecord $category,
        public string $name,
        public string $description,
        public int $priceMin,
        public int $priceMax,
        public ?string $image = null,
        public int $stock = 0,
        public array $variants = [],
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
        public ?string $deletedAt = null,
    ) {
        $this->category = $category instanceof Category ? $category->toRecord() : $category;
        $this->variants = self::normaliseVariants($variants);
    }

    /**
     * Coerce any variant input (plain label strings or {name, price} maps) into
     * a clean list of ['name' => string, 'price' => int|null] entries.
     *
     * @param  array<int, mixed>  $variants
     * @return array<int, array{name: string, price: int|null}>
     */
    public static function normaliseVariants(array $variants): array
    {
        $clean = [];

        foreach ($variants as $variant) {
            $name = is_string($variant) ? trim($variant) : trim((string) ($variant['name'] ?? ''));

            if ($name === '') {
                continue;
            }

            $price = is_array($variant) && isset($variant['price']) && is_numeric($variant['price'])
                ? (int) $variant['price']
                : null;

            $clean[] = ['name' => $name, 'price' => $price];
        }

        return $clean;
    }

    /**
     * @return array<int, array{name: string, price: int|null}>
     */
    private static function decodeVariants(mixed $raw): array
    {
        if (is_array($raw)) {
            return self::normaliseVariants($raw);
        }

        if (! is_string($raw) || trim($raw) === '') {
            return [];
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? self::normaliseVariants($decoded) : [];
    }

    /**
     * The ID column exactly as printed in the source document (1-5 per subtopic).
     */
    public function displayId(): string
    {
        return (string) $this->categoryId;
    }

    public function slug(): string
    {
        return Str::slug($this->name);
    }

    public function hasPriceRange(): bool
    {
        return $this->priceMin !== $this->priceMax;
    }

    public function hasVariants(): bool
    {
        return $this->variants !== [];
    }

    /**
     * The price for a variant at a given index: its own price when one was set,
     * otherwise a value spread evenly across the product's price range so the
     * price still scales with the option picked.
     */
    public function variantPriceAt(int $index): int
    {
        $variant = $this->variants[$index] ?? null;

        if ($variant !== null && ($variant['price'] ?? 0) > 0) {
            return (int) $variant['price'];
        }

        $count = count($this->variants);

        if ($count <= 1 || ! $this->hasPriceRange()) {
            return $this->priceMin;
        }

        $step = ($this->priceMax - $this->priceMin) / ($count - 1);

        return (int) round($this->priceMin + $step * $index);
    }

    /** The price for a named variant, or null when the product has no such variant. */
    public function variantPrice(string $name): ?int
    {
        foreach ($this->variants as $index => $variant) {
            if ($variant['name'] === $name) {
                return $this->variantPriceAt($index);
            }
        }

        return null;
    }

    /** e.g. "5,000" */
    public function formattedMin(): string
    {
        return number_format($this->priceMin);
    }

    /** e.g. "10,000" */
    public function formattedMax(): string
    {
        return number_format($this->priceMax);
    }

    /** e.g. "₱5,000–10,000" — byte for byte the string used in the document. */
    public function priceLabel(): string
    {
        if (! $this->hasPriceRange()) {
            return '₱'.$this->formattedMin();
        }

        return '₱'.$this->formattedMin().'–'.$this->formattedMax();
    }

    /** Midpoint used for sorting and chart positioning. */
    public function averagePrice(): float
    {
        return ($this->priceMin + $this->priceMax) / 2;
    }

    public function formattedAveragePrice(): string
    {
        return number_format($this->averagePrice(), 0);
    }

    /** Highest figure of the range, used for the "top price" summary tile. */
    public function ceiling(): float
    {
        return (float) max($this->priceMin, $this->priceMax);
    }

    public function imageUrl(): string
    {
        if ($this->image === null || $this->image === '') {
            return asset('images/products/placeholder.svg');
        }

        // Already a URL or a root-relative path (uploads are stored on the
        // "public" disk and referenced as /storage/images/products/...).
        if (Str::startsWith($this->image, ['http://', 'https://', '/'])) {
            return $this->image;
        }

        // Uploads stored on the public disk: keep the /storage prefix.
        if (Str::startsWith($this->image, 'storage/')) {
            return asset($this->image);
        }

        // Seed images from the document live directly in public/images/products.
        return asset('images/products/'.$this->image);
    }

    public function initials(): string
    {
        $words = preg_split('/[\s\/]+/', trim($this->name)) ?: [];
        $letters = array_map(
            fn (string $word) => Str::upper(Str::substr($word, 0, 1)),
            array_slice(array_filter($words), 0, 2)
        );

        return $letters === [] ? 'TC' : implode('', $letters);
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    // ------------------------------------------------------------- inventory --

    public function inStock(): bool
    {
        return $this->stock > 0;
    }

    public function isLowStock(int $threshold = 5): bool
    {
        return $this->stock <= $threshold;
    }

    public function stockLabel(): string
    {
        return $this->stock.' in stock';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->categoryId,
            'category' => $this->category->value,
            'name' => $this->name,
            'description' => $this->description,
            'price_min' => $this->priceMin,
            'price_max' => $this->priceMax,
            'image' => $this->image,
            'stock' => $this->stock,
            'variants' => $this->variants,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'deleted_at' => $this->deletedAt,
        ];
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  array<string, CategoryRecord>  $categoryMap  name => record
     */
    public static function fromArray(array $row, array $categoryMap = []): self
    {
        $name = (string) ($row['category'] ?? '');
        $category = $categoryMap[$name]
            ?? Category::tryFrom($name)?->toRecord()
            ?? CategoryRecord::fromName($name);

        return new self(
            id: (int) ($row['id'] ?? 0),
            categoryId: (int) ($row['category_id'] ?? 0),
            category: $category,
            name: (string) ($row['name'] ?? ''),
            description: (string) ($row['description'] ?? ''),
            priceMin: (int) ($row['price_min'] ?? 0),
            priceMax: (int) ($row['price_max'] ?? 0),
            image: isset($row['image']) && $row['image'] !== '' ? (string) $row['image'] : null,
            stock: (int) ($row['stock'] ?? 0),
            variants: self::decodeVariants($row['variants'] ?? null),
            createdAt: $row['created_at'] ?? null,
            updatedAt: $row['updated_at'] ?? null,
            deletedAt: $row['deleted_at'] ?? null,
        );
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
