<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoomTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Single Room', 'Double Room', 'Deluxe Room', 'Suite', 'Family Room', 'Presidential Suite',
                'Studio', 'King Room', 'Queen Room', 'Twin Room'
            ]),
            'description' => $this->faker->sentence(8),
            'base_price' => $this->faker->randomFloat(2, 50, 1000), // price from $50 to $1000
            'max_occupancy' => $this->faker->numberBetween(1, 6),
        ];
    }
}
