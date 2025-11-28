<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // public function up(): void
    // {
    //     Schema::create('sub_categories', function (Blueprint $table) {
    //         $table->id();
    //         $table->unsignedBigInteger('category_id'); // bigint UNSIGNED NOT NULL
    //         $table->string('name', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
    //         $table->string('slug', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->unique();
    //         $table->text('description')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
    //         $table->integer('status');
    //         $table->string('meta_tag', 255)->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
    //         $table->string('meta_description', 500)->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
    //         $table->string('file_name', 255)->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
    //         $table->string('org_name', 255)->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
    //         $table->string('file_name_default', 255)->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
    //         $table->string('org_name_default', 255)->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
    //         $table->string('type', 255)->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
    //         $table->timestamp('deleted_at')->nullable();
    //         $table->timestamps();

    //         $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
    //     });
    // }
    public function up(): void
{
    // Create subcategories table
    Schema::create('sub_categories', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('category_id'); // foreign key to categories table
        $table->unsignedBigInteger('parent_id')->nullable(); // Nullable to allow root-level subcategories
        $table->integer('level')->default(1); // Default level is 1 for root-level subcategories
        $table->string('name', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
        $table->string('slug', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->unique();
        $table->text('description')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
        $table->integer('status');
        $table->string('meta_tag', 255)->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
        $table->string('meta_description', 500)->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
        $table->string('image_sub_cat', 255)->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
        $table->string('product_default_sub_cat', 255)->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
        $table->softDeletes(); // Enables soft deletes
        $table->timestamps();

        // Foreign key to categories table
        $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_categories');
    }
};
