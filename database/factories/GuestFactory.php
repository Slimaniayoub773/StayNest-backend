<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class GuestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'identification_number' => $this->faker->unique()->numerify('ID#######'), // Example: ID1234567
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'remember_token' => \Str::random(10),
            'is_blocked' => false,
            'google_id' => null,
        ];
    }
}
