<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->index();
            // "in" (stock received), "out" (sold/removed) or "adjust" (correction).
            $table->string('type', 16)->index();
            // Signed change: +5 for a receive, -2 for a sale.
            $table->integer('quantity');
            // Resulting stock level after the movement was applied.
            $table->unsignedInteger('balance')->default(0);
            $table->string('reason', 160)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
