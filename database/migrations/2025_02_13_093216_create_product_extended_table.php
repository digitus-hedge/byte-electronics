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
        Schema::create('product_extended', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key 'id'
            $table->unsignedBigInteger('product_id'); // Foreign key referencing 'products' table
            $table->unsignedBigInteger('product_attr_id'); // Foreign key referencing 'product_attributes' table
            $table->string('value_type'); // 'value_type' column for the type of value (e.g., string, integer, boolean)
            $table->timestamps(); // 'created_at' and 'updated_at' columns
    
            // Add foreign key constraints to product_id and product_attr_id
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('product_attr_id')->references('id')->on('product_attributes')->onDelete('cascade');
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_extended');
    }
};
