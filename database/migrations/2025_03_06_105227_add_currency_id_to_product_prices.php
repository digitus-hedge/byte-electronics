<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->unsignedBigInteger('currency_id')->after('currency_symbol')->nullable();
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('set null');
        });
    }

    public function down() {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn('currency_id');
        });
    }
};
