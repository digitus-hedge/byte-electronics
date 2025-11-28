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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('slug', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->unique();
            $table->text('description')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
        
            $table->integer('status');
            $table->string('meta_tag', 255)->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('meta_description', 500)->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('file_name', 255)->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('org_name', 255)->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('type', 255)->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
        
            // Use softDeletes to create 'deleted_at' column
            $table->softDeletes();
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
