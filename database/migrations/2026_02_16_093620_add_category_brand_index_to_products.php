<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('products', function (Blueprint $table) {
        $table->index(['status', 'deleted_at', 'category_id', 'brand_id', 'created_at'], 'products_category_brand_index');
    });
}

public function down()
{
    Schema::table('products', function (Blueprint $table) {
        $table->dropIndex('products_category_brand_index');
    });
}
};
