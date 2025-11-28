<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id()->unsigned();
            $table->string('title')->collation('utf8mb4_unicode_ci');
            $table->string('author')->nullable()->collation('utf8mb4_unicode_ci');
            $table->date('publish_date')->nullable();
            $table->text('content')->collation('utf8mb4_unicode_ci');
            $table->text('content_1')->collation('utf8mb4_unicode_ci');
            $table->string('categories')->nullable()->collation('utf8mb4_unicode_ci');
            $table->string('tags')->nullable()->collation('utf8mb4_unicode_ci');
            $table->text('summary')->nullable()->collation('utf8mb4_unicode_ci');
            $table->string('featured_image')->nullable()->collation('utf8mb4_unicode_ci');
            $table->string('image_1')->nullable()->collation('utf8mb4_unicode_ci');
            $table->string('image_2')->nullable()->collation('utf8mb4_unicode_ci');
            $table->string('image_3')->nullable()->collation('utf8mb4_unicode_ci');
            $table->string('image_4')->nullable()->collation('utf8mb4_unicode_ci');
            $table->string('seo_title')->nullable()->collation('utf8mb4_unicode_ci');
            $table->text('meta_description')->nullable()->collation('utf8mb4_unicode_ci');
            $table->string('slug')->collation('utf8mb4_unicode_ci')->unique();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->collation('utf8mb4_unicode_ci');
            $table->boolean('social_sharing')->default(1);
            $table->timestamps(0);  // For `created_at` and `updated_at` timestamps
            $table->softDeletes();  // For `deleted_at` soft delete column
        });
    }

    public function down()
    {
        Schema::dropIfExists('blogs');
    }
};

