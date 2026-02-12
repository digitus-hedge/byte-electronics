<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::table('product_extended', function (Blueprint $table) {
    $table->index(['product_id', 'id'], 'idx_pe_product_id');
});

Schema::table('product_attr_documents', function (Blueprint $table) {
    $table->index(['product_ext_id', 'name'], 'idx_pad_ext_name');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_ext', function (Blueprint $table) {
            //
        });
    }
};
