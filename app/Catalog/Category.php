<?php

namespace App\Catalog;

use Illuminate\Support\Str;

/**
 * The five subtopics defined by the source document.
 * Order here is the order used by the sidebar, the dashboard and the tables.
 */
enum Category: string
{
    case ComputerComponents = 'Computer Components';
    case StorageDevices = 'Storage Devices';
    case ComputerAccessories = 'Computer Accessories';
    case NetworkingComponents = 'Networking Components';
    case PowerAndConnection = 'Power & Connection';

    public function label(): string
    {
        return $this->value;
    }

    public function slug(): string
    {
        return match ($this) {
            self::ComputerComponents => 'computer-components',
            self::StorageDevices => 'storage-devices',
            self::ComputerAccessories => 'computer-accessories',
            self::NetworkingComponents => 'networking-components',
            self::PowerAndConnection => 'power-connection',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::ComputerComponents => 'Computer',
            self::StorageDevices => 'Storage',
            self::ComputerAccessories => 'Accessories',
            self::NetworkingComponents => 'Networking',
            self::PowerAndConnection => 'Power',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ComputerComponents => 'Manage and browse computer component products.',
            self::StorageDevices => 'Manage and browse storage device products.',
            self::ComputerAccessories => 'Manage and browse computer accessory products.',
            self::NetworkingComponents => 'Manage and browse networking component products.',
            self::PowerAndConnection => 'Manage and browse power and connection products.',
        };
    }

    /** Two letter monogram used by the category badges and the sidebar icons. */
    public function monogram(): string
    {
        return match ($this) {
            self::ComputerComponents => 'CC',
            self::StorageDevices => 'SD',
            self::ComputerAccessories => 'CA',
            self::NetworkingComponents => 'NC',
            self::PowerAndConnection => 'PC',
        };
    }

    public static function slugFor(string $label): ?string
    {
        foreach (self::cases() as $case) {
            if ($case->value === $label) {
                return $case->slug();
            }
        }

        return null;
    }

    public static function fromSlug(string $slug): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->slug() === Str::slug($slug)) {
                return $case;
            }
        }

        return null;
    }

    /** @return array<int, self> */
    public static function ordered(): array
    {
        return self::cases();
    }

    /**
     * The database-backed record for this default subtopic. Used by the seeder
     * and when a product built straight from CatalogData (which still speaks in
     * enum cases) is converted to the runtime value object.
     */
    public function toRecord(): CategoryRecord
    {
        $position = (int) array_search($this, self::cases(), true);

        return new CategoryRecord(
            id: $position + 1,
            slug: $this->slug(),
            value: $this->value,
            short: $this->shortLabel(),
            description: $this->description(),
            monogram: $this->monogram(),
            position: $position,
        );
    }

    /** @return array<int, string> */
    public static function labels(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}
