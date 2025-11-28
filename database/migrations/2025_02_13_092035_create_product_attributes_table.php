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
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key 'id'
            $table->unsignedBigInteger('prd_header_id'); // Foreign key referencing 'product_headers' table
            $table->string('name'); // 'name' column for the attribute
            $table->string('value_type'); // 'value_type' column for the type of value (e.g., string, integer, boolean)
            $table->timestamps(); // 'created_at' and 'updated_at' columns
    
            // Add foreign key constraint to prd_header_id (assuming 'product_headers' table exists)
            $table->foreign('prd_header_id')->references('id')->on('product_headers')->onDelete('cascade');
        });
    
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attributes');
    }
};
