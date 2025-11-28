<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['name' => 'United States', 'country_code' => '1', 'short_name' => 'USA'],
            ['name' => 'Canada', 'country_code' => '1', 'short_name' => 'CAN'],
            ['name' => 'United Kingdom', 'country_code' => '44', 'short_name' => 'UK'],
            ['name' => 'Australia', 'country_code' => '61', 'short_name' => 'AUS'],
            ['name' => 'India', 'country_code' => '91', 'short_name' => 'IND'],
            ['name' => 'China', 'country_code' => '86', 'short_name' => 'CHN'],
            ['name' => 'Germany', 'country_code' => '49', 'short_name' => 'DEU'],
            ['name' => 'France', 'country_code' => '33', 'short_name' => 'FRA'],
            ['name' => 'Japan', 'country_code' => '81', 'short_name' => 'JPN'],
            ['name' => 'Brazil', 'country_code' => '55', 'short_name' => 'BRA'],
            ['name' => 'South Africa', 'country_code' => '27', 'short_name' => 'ZAF'],
            ['name' => 'Mexico', 'country_code' => '52', 'short_name' => 'MEX'],
            ['name' => 'Russia', 'country_code' => '7', 'short_name' => 'RUS'],
            ['name' => 'Italy', 'country_code' => '39', 'short_name' => 'ITA'],
            ['name' => 'Spain', 'country_code' => '34', 'short_name' => 'ESP'],
            ['name' => 'Nigeria', 'country_code' => '234', 'short_name' => 'NGA'],
            ['name' => 'Argentina', 'country_code' => '54', 'short_name' => 'ARG'],
            ['name' => 'South Korea', 'country_code' => '82', 'short_name' => 'KOR'],
            ['name' => 'Saudi Arabia', 'country_code' => '966', 'short_name' => 'SAU'],
            ['name' => 'Turkey', 'country_code' => '90', 'short_name' => 'TUR'],
            ['name' => 'Sweden', 'country_code' => '46', 'short_name' => 'SWE'],
            ['name' => 'Switzerland', 'country_code' => '41', 'short_name' => 'CHE'],
            ['name' => 'Netherlands', 'country_code' => '31', 'short_name' => 'NLD'],
            ['name' => 'Norway', 'country_code' => '47', 'short_name' => 'NOR'],
            ['name' => 'Denmark', 'country_code' => '45', 'short_name' => 'DNK'],
            ['name' => 'Belgium', 'country_code' => '32', 'short_name' => 'BEL'],
            ['name' => 'Austria', 'country_code' => '43', 'short_name' => 'AUT'],
            ['name' => 'Singapore', 'country_code' => '65', 'short_name' => 'SGP'],
            ['name' => 'New Zealand', 'country_code' => '64', 'short_name' => 'NZL'],
            ['name' => 'Israel', 'country_code' => '972', 'short_name' => 'ISR'],
            ['name' => 'Egypt', 'country_code' => '20', 'short_name' => 'EGY'],
            ['name' => 'Philippines', 'country_code' => '63', 'short_name' => 'PHL'],
            ['name' => 'Vietnam', 'country_code' => '84', 'short_name' => 'VNM'],
            ['name' => 'Thailand', 'country_code' => '66', 'short_name' => 'THA'],
            ['name' => 'Malaysia', 'country_code' => '60', 'short_name' => 'MYS'],
            ['name' => 'Pakistan', 'country_code' => '92', 'short_name' => 'PAK'],
            ['name' => 'Bangladesh', 'country_code' => '880', 'short_name' => 'BGD'],
            ['name' => 'Indonesia', 'country_code' => '62', 'short_name' => 'IDN'],
            ['name' => 'Chile', 'country_code' => '56', 'short_name' => 'CHL'],
            ['name' => 'Colombia', 'country_code' => '57', 'short_name' => 'COL'],
            ['name' => 'Venezuela', 'country_code' => '58', 'short_name' => 'VEN'],
            ['name' => 'Peru', 'country_code' => '51', 'short_name' => 'PER'],
            ['name' => 'Poland', 'country_code' => '48', 'short_name' => 'POL'],
            ['name' => 'Czech Republic', 'country_code' => '420', 'short_name' => 'CZE'],
            ['name' => 'Hungary', 'country_code' => '36', 'short_name' => 'HUN'],
            ['name' => 'Portugal', 'country_code' => '351', 'short_name' => 'PRT'],
            ['name' => 'Greece', 'country_code' => '30', 'short_name' => 'GRC'],
            ['name' => 'Romania', 'country_code' => '40', 'short_name' => 'ROU'],
            ['name' => 'Ukraine', 'country_code' => '380', 'short_name' => 'UKR'],
            ['name' => 'United Arab Emirates', 'country_code' => '971', 'short_name' => 'UAE'],
            ['name' => 'Oman', 'country_code' => '968', 'short_name' => 'OMN'],
            ['name' => 'Bahrain', 'country_code' => '973', 'short_name' => 'BHR'],
            ['name' => 'Iran', 'country_code' => '98', 'short_name' => 'IRN'],
            ['name' => 'Iraq', 'country_code' => '964', 'short_name' => 'IRQ'],
            ['name' => 'Jordan', 'country_code' => '962', 'short_name' => 'JOR'],
            ['name' => 'Kuwait', 'country_code' => '965', 'short_name' => 'KWT'],
            ['name' => 'Lebanon', 'country_code' => '961', 'short_name' => 'LBN'],
            ['name' => 'Palestine', 'country_code' => '970', 'short_name' => 'PS'],
            ['name' => 'Qatar', 'country_code' => '974', 'short_name' => 'QAT'],
            ['name' => 'Afghanistan', 'country_code' => '93', 'short_name' => 'AFG'],
            ['name' => 'Albania', 'country_code' => '355', 'short_name' => 'ALB'],
            ['name' => 'Algeria', 'country_code' => '213', 'short_name' => 'DZA'],
        ];

        // Insert data
        DB::table('countries')->insert($countries);
    }
}
