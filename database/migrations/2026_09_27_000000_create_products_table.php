<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            // Plain integer PK, not auto-increment: the app computes the next
            // id itself to keep ids aligned with the source document.
            $table->unsignedInteger('id')->primary();
            $table->unsignedSmallInteger('category_id');
            $table->string('category', 64);
            $table->string('name');
            $table->text('description');
            $table->unsignedInteger('price_min');
            $table->unsignedInteger('price_max');
            $table->string('image')->nullable();
            $table->index('category');
            $table->index(['price_min', 'price_max']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};