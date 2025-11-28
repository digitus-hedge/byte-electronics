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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('bannername');
            $table->text('type');
            $table->integer('priority')->default(1);;
            $table->boolean('status')->default(true);
            $table->string('url1');
            $table->string('url2')->nullable();
            $table->string('url3')->nullable();
            $table->text('pagename');
            $table->text('redirect_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
