<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('password'), // default password
            'role_id' => Role::inRandomOrder()->first()->id ?? Role::factory(), // assign random role
            'email_verified_at' => now(),
            'remember_token' => \Str::random(10),
            'phone' => $this->faker->phoneNumber,
            'is_active' => true,
            'is_blocked' => false,
            'google_id' => null,
        ];
    }
}
