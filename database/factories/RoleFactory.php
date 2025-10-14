<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'role_name' => $this->faker->unique()->jobTitle(),
            'description' => $this->faker->sentence(8),
        ];
    }
}
