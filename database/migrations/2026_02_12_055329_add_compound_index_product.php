<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index(
                ['category_id', 'status', 'deleted_at', 'brand_id'],
                'idx_products_cat_status_del_brand'
            );
            $table->index(
                ['category_id', 'deleted_at', 'created_at'],
                'idx_products_cat_del_created'
            );
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_cat_status_del_brand');
            $table->dropIndex('idx_products_cat_del_created');
        });
    }
};
