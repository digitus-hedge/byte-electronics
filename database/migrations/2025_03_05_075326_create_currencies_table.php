<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    
            public function up()
            {
                Schema::create('currencies', function (Blueprint $table) {
                    $table->id();
                    $table->string('currency_code')->unique();
                    $table->string('currency_symbol')->nullable();
                    $table->decimal('conversion_rate', 10, 4);  // Example for a decimal with 4 decimal places
                    $table->boolean('status')->default(true);  // To mark active currencies
                    $table->boolean('is_default')->default(false);  // To mark default currency
                    $table->timestamps();
                });
            }

        
        
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
