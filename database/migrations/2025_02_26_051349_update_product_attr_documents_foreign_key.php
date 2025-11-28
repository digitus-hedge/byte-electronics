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
        Schema::table('product_attr_documents', function (Blueprint $table) {
            // Drop the incorrect foreign key first
            $table->dropForeign(['product_ext_id']);
            
            // Add the correct foreign key reference to product_extended(id)
            $table->foreign('product_ext_id')
                ->references('id')->on('product_extended')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_attr_documents', function (Blueprint $table) {
            // Rollback: Drop the correct foreign key
            $table->dropForeign(['product_ext_id']);

            // Restore the incorrect foreign key (if needed)
            $table->foreign('product_ext_id')
                ->references('id')->on('products')
                ->onDelete('cascade');
        });
    }
};
