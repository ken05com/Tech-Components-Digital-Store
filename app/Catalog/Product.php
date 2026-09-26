<?php

namespace App\Catalog;

use Illuminate\Support\Str;

/**
 * A single catalog row. This is a plain value object on purpose: the project
 * intentionally ships without a database, so nothing here extends Eloquent.
 */
final class Product implements \JsonSerializable
{
    public function __construct(
        public int $id,
        public int $categoryId,
        public Category $category,
        public string $name,
        public string $description,
        public int $priceMin,
        public int $priceMax,
        public ?string $image = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
    ) {}

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
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    public static function fromArray(array $row): self
    {
        $category = Category::tryFrom((string) ($row['category'] ?? '')) ?? Category::ComputerComponents;

        return new self(
            id: (int) ($row['id'] ?? 0),
            categoryId: (int) ($row['category_id'] ?? 0),
            category: $category,
            name: (string) ($row['name'] ?? ''),
            description: (string) ($row['description'] ?? ''),
            priceMin: (int) ($row['price_min'] ?? 0),
            priceMax: (int) ($row['price_max'] ?? 0),
            image: isset($row['image']) && $row['image'] !== '' ? (string) $row['image'] : null,
            createdAt: $row['created_at'] ?? null,
            updatedAt: $row['updated_at'] ?? null,
        );
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
