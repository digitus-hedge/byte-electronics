<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('products', function (Blueprint $table) {
        $table->index('created_at');
        $table->index('best_sellers');
        $table->index('deleted_at');
        // Composite index for the exact queries
        $table->index(['deleted_at', 'created_at']);
        $table->index(['best_sellers', 'deleted_at', 'created_at']);
    });

    Schema::table('product_prices', function (Blueprint $table) {
        $table->index('product_id');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
