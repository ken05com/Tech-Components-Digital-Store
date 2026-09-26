<?php

namespace App\Catalog;

/**
 * Chart colours, kept in one place so the doughnut, the legend swatches and the
 * quick-link tiles always agree with each other. Orange leads, the rest are
 * supporting hues that still sit comfortably on a near-black surface.
 */
final class ChartPalette
{
    private const COLORS = [
        'computer-components' => '#f97316',
        'storage-devices' => '#fb923c',
        'computer-accessories' => '#38bdf8',
        'networking-components' => '#a78bfa',
        'power-connection' => '#34d399',
    ];

    public static function color(string $slug): string
    {
        return self::COLORS[$slug] ?? '#94a3b8';
    }

    /** @return array<int, string> */
    public static function forSlugs(iterable $slugs): array
    {
        return array_map(fn (string $slug) => self::color($slug), $slugs);
    }

    public static function withAlpha(string $slug, float $alpha = 0.12): string
    {
        [$r, $g, $b] = sscanf(self::color($slug), '#%02x%02x%02x');

        return sprintf('rgba(%d, %d, %d, %s)', $r, $g, $b, $alpha);
    }
}
