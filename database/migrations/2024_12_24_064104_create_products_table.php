<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**sddsdsd
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('brand_id');
            $table->foreign('brand_id')->references('id')->on('brands');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->string('file_name', 255)->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('org_name', 255)->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->unsignedBigInteger('sub_category_id')->nullable();
            $table->foreign('sub_category_id')->references('id')->on('sub_categories');
            $table->string('manufacturers_no')->nullable();
            $table->string('price')->nullable();
            $table->text('description')->nullable();
            $table->text('more_description')->nullable();
            $table->text('tag');
            $table->integer('minimum_qty')->default(1);
            $table->integer('is_repairable')->default(1);
            $table->integer('featured')->default(0);
            $table->integer('status')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
