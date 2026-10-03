<?php

namespace Database\Seeders;

use App\Catalog\CatalogData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        foreach (CatalogData::products() as $product) {
            DB::table('products')->updateOrInsert(
                ['id' => $product->id],
                [
                    'category_id' => $product->categoryId,
                    'category'    => $product->category->value,
                    'name'        => $product->name,
                    'description' => $product->description,
                    'price_min'   => $product->priceMin,
                    'price_max'   => $product->priceMax,
                    'image'       => $product->image,
                ],
            );
        }
    }
}