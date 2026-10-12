<?php

namespace Database\Seeders;

use App\Catalog\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds the five subtopics from the source document into the categories table.
 *
 * Category management is database-backed now, so these five rows are only the
 * starting point: an admin can rename, reorder, add to or delete from them at
 * runtime. Existing rows are left untouched so edits survive a re-seed.
 */
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (Category::ordered() as $case) {
            $record = $case->toRecord();

            $exists = DB::table('categories')->where('name', $record->value)->exists();

            if ($exists) {
                continue;
            }

            DB::table('categories')->insert([
                'slug' => $record->slug,
                'name' => $record->value,
                'short_label' => $record->short,
                'description' => $record->description,
                'monogram' => $record->monogram,
                'position' => $record->position,
                'created_at' => now()->format('Y-m-d H:i:s'),
                'updated_at' => now()->format('Y-m-d H:i:s'),
            ]);
        }
    }
}
