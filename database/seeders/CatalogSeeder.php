<?php

namespace Database\Seeders;

use App\Catalog\CatalogData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        foreach (CatalogData::products() as $index => $product) {
            DB::table('products')->updateOrInsert(
                ['id' => $product->id],
                [
                    'category_id' => $product->categoryId,
                    'category' => $product->category->value,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price_min' => $product->priceMin,
                    'price_max' => $product->priceMax,
                    'image' => $product->image,
                    'variants' => $product->variants === [] ? null : json_encode($product->variants),
                    // Deterministic opening stock: every sixth row starts low so
                    // the low-stock views have something to show out of the box.
                    'stock' => $index % 6 === 0 ? 3 : 12 + (($index * 7) % 30),
                ],
            );
        }
    }
}
