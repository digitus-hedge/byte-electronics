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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('session_id')->nullable()->after('user_id');

            // If you want a foreign key constraint (optional, depends on how your sessions table is structured)
            // $table->foreign('session_id')->references('id')->on('sessions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // If foreign key was used:
            // $table->dropForeign(['session_id']);
            $table->dropColumn('session_id');
        });
    }
};
