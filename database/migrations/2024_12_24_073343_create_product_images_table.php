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
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->string('image_name'); // Name of the image
            $table->string('image_path'); // Path to the image file
            $table->unsignedBigInteger('product_id'); // Foreign key for products
            $table->string('product_name'); // Product name for quick reference
            $table->integer('priority')->default(0); // Priority for ordering images
            $table->string('redirect_url')->nullable(); // Optional redirect URL
            $table->timestamps();
            $table->softDeletes(); // For soft deletion of records

            // Foreign key constraint
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
