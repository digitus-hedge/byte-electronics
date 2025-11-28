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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('carts')->onDelete('cascade'); // Belongs to a cart
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Belongs to a product
            $table->integer('quantity')->default(1); // Quantity of the product
            $table->decimal('price', 8, 2); // Price of the product at the time of adding to cart
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
