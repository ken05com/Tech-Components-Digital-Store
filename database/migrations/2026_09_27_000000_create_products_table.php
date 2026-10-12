<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            // String PK, not auto-increment: the app computes the next id
            // itself to keep ids aligned with the source document. Every
            // column is VARCHAR so values such as "₱5,000–10,000" can be stored.
            $table->string('id', 20)->primary();
            $table->string('category_id', 20);
            $table->string('category', 64);
            $table->string('name', 120);
            $table->string('description', 255);
            $table->string('price_min', 20);
            $table->string('price_max', 20);
            $table->string('image', 255)->nullable();
            $table->index('category');
            $table->index(['price_min', 'price_max']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
