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
        Schema::table('product_extended', function (Blueprint $table) {
            $table->unsignedBigInteger('prd_header_id')->nullable()->after('id'); // Add column
            $table->foreign('prd_header_id')->references('id')->on('product_headers')->onDelete('set null'); // Foreign key
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_extended', function (Blueprint $table) {
            $table->dropForeign(['prd_header_id']);
            $table->dropColumn('prd_header_id');
        });

    }
};
