<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Booking;

class BookingStatusHistoryFactory extends Factory
{
    public function definition(): array
    {
        $status = $this->faker->randomElement(['pending', 'confirmed', 'cancelled', 'checked_in', 'checked_out']);

        return [
            'booking_id' => Booking::inRandomOrder()->value('id'),
            'status' => $status,
            'changed_at' => $this->faker->dateTimeBetween('-2 weeks', 'now'),
            'changed_by' => $this->faker->optional()->name,
            'notes' => $this->faker->optional()->sentence,
        ];
    }
}
