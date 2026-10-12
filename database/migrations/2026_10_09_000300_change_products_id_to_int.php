<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The products table was originally created with a VARCHAR primary key.
        // Every value in it is numeric, so the id can be a proper unsigned
        // integer column. The app already keeps ids aligned with the source
        // document by computing the next one itself.
        Schema::table('products', function (Blueprint $table) {
            $table->dropPrimary();
            $table->unsignedBigInteger('id')->change();
            $table->primary('id');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropPrimary();
            $table->string('id', 20)->change();
            $table->primary('id');
        });
    }
};
