<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Currency::create([
            'currency_code' => 'AED',
            'currency_symbol' => 'AED',
            'conversion_rate' => 1.0000,
            'status' => true,
            'is_default' => true, // Setting AED as default
        ]);

        Currency::create([
            'currency_code' => 'USD',
            'currency_symbol' => '$',
            'conversion_rate' => 3.3800,
            'status' => true,
            'is_default' => false,
        ]);
    }
}
