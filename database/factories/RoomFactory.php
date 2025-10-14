<?php

namespace Database\Factories;

use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    public function definition(): array
    {
        $statuses = ['available', 'booked', 'maintenance'];

        return [
            'room_number' => $this->faker->unique()->numerify('1##'), // e.g., 101, 105
            'type_id' => RoomType::inRandomOrder()->first()->id ?? RoomType::factory(),
            'floor_number' => $this->faker->numberBetween(1, 10),
            'price_per_night' => $this->faker->randomFloat(2, 80, 1000),
            'description' => $this->faker->sentence(8),
            'status' => $this->faker->randomElement($statuses),
        ];
    }
}
