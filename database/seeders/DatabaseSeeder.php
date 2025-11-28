<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        // Create Admin User
        $admin = User::factory()->withPersonalTeam()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
        ]);

        // Assign the Admin Role to the Admin User
        $admin->assignRole('admin');

        // Create Regular User
        $user = User::factory()->withPersonalTeam()->create([
            'name' => 'Regular User',
            'email' => 'user@gmail.com',
            'password' => Hash::make('admin123'),
        ]);

        // Assign the User Role to the Regular User
        $user->assignRole('user');
    }
}
