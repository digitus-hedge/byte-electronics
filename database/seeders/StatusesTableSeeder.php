<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $statuses = [
            [
                'name' => 'Pending',
                'description' => 'For Pending Orders',
                'flow_control' => 'Order',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Confirmed',
                'description' => 'Order has been confirmed',
                'flow_control' => 'Order',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Completed',
                'description' => 'Order has been completed',
                'flow_control' => 'Order',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Cancelled',
                'description' => 'Order has been cancelled',
                'flow_control' => 'Order',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Cancelled',
                'description' => 'Order has been cancelled',
                'flow_control' => 'Order',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
             [
                'name' => 'Rejected',
                'description' => 'Order has been Rejected',
                'flow_control' => 'Order',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($statuses as $status) {
            DB::table('statuses')->updateOrInsert(
                ['name' => $status['name']],
                $status
            );
        }
    }
}
