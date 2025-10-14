<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure roles exist
        $adminRole = Role::firstWhere('role_name', 'Admin') ?? Role::create([
            'role_name' => 'Admin',
            'description' => 'Oversees all hotel operations and manages staff.',
        ]);

        // Create a specific admin user if not exists
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('admin123'),
                'role_id' => $adminRole->id,
                'email_verified_at' => now(),
                'phone' => '1234567890',
                'is_active' => true,
                'is_blocked' => false,
            ]
        );

        // Get all other roles except Admin
        $roles = Role::where('role_name', '!=', 'Admin')->pluck('id')->toArray();

        // Generate 20 random users with random roles
        User::factory()
            ->count(20)
            ->state(function () use ($roles) {
                return [
                    'role_id' => $roles[array_rand($roles)],
                    'is_active' => true,
                    'is_blocked' => false,
                ];
            })
            ->create();
    }
}
