<?php

namespace App\Catalog;

use Illuminate\Support\Str;

/**
 * A category row as stored in the "categories" table.
 *
 * Categories used to be a fixed enum. The five subtopics from the source
 * document are still the seed data, but they now live in the database so an
 * admin can add, rename, reorder and delete them. This value object exposes the
 * same surface the old enum did (label/slug/shortLabel/description/monogram)
 * plus a public $value, so the product layer and the Blade views keep working
 * unchanged.
 */
final class CategoryRecord implements \JsonSerializable
{
    public function __construct(
        public int $id,
        public string $slug,
        public string $value,
        public string $short,
        public string $description,
        public string $monogram,
        public int $position = 0,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
    ) {}

    public function label(): string
    {
        return $this->value;
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function shortLabel(): string
    {
        return $this->short;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function monogram(): string
    {
        return $this->monogram;
    }

    public function is(CategoryRecord $other): bool
    {
        return $this->id === $other->id;
    }

    /**
     * A safe stand-in for a category name that no longer exists in the table
     * (for example a product left behind after a category was deleted).
     */
    public static function fromName(string $name): self
    {
        $name = trim($name) !== '' ? trim($name) : 'Uncategorised';
        $words = preg_split('/[\s&\/]+/', $name) ?: [];
        $letters = array_map(
            fn (string $word) => Str::upper(Str::substr($word, 0, 1)),
            array_slice(array_filter($words), 0, 2)
        );

        return new self(
            id: 0,
            slug: Str::slug($name),
            value: $name,
            short: (string) ($words[0] ?? $name),
            description: '',
            monogram: $letters === [] ? '??' : implode('', $letters),
            position: 999,
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->value,
            'short_label' => $this->short,
            'description' => $this->description,
            'monogram' => $this->monogram,
            'position' => $this->position,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
