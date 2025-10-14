<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Room;
use App\Models\User;

class CleaningScheduleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'room_id' => Room::inRandomOrder()->value('id'),
            'cleaner_id' => User::inRandomOrder()->value('id'), // ideally, limit to cleaners
            'cleaning_date' => $this->faker->dateTimeBetween('now', '+2 weeks')->format('Y-m-d'),
            'cleaning_status' => $this->faker->randomElement(['pending', 'in_progress', 'completed']),
            'priority_level' => $this->faker->randomElement(['low', 'normal', 'high']),
            'notes' => $this->faker->optional()->sentence,
        ];
    }
}
