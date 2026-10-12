<?php

namespace App\Catalog;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Central place for storing and removing uploaded product images, so paths stay
 * safe and only genuinely replaced/removed files are ever deleted.
 */
class ProductImageStore
{
    /** Stores an uploaded image on the public disk and returns a root-relative URL. */
    public function store(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        $file = $request->file('image');
        $name = Str::slug(pathinfo((string) $request->input('name'), PATHINFO_FILENAME)) ?: 'product';
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = $name.'-'.Str::lower(Str::random(8)).'.'.$extension;

        Storage::disk('public')->putFileAs('images/products', $file, $filename);

        // Uploads live on the "public" disk, so hand back a root-relative path
        // (the 25 seed images sit directly in public/images/products instead).
        return '/'.ltrim(Storage::disk('public')->url('images/products/'.$filename), '/');
    }

    /**
     * Deletes an uploaded image. Seed images (bare filenames served straight
     * from public/images/products) and shared/unrelated paths are never touched.
     */
    public function delete(?string $image): void
    {
        if ($image === null || ! Str::startsWith($image, '/storage/')) {
            return;
        }

        Storage::disk('public')->delete(Str::after($image, '/storage/'));
    }
}
