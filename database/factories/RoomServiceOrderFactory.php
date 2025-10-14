<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Guest;

class RoomServiceOrderFactory extends Factory
{
    public function definition(): array
    {
        $booking = Booking::inRandomOrder()->first();

        return [
            'booking_id' => $booking?->id ?? 1,
            'room_id' => $booking?->room_id ?? 1,
            'guest_id' => $booking?->guest_id ?? 1,
            'order_date' => now(),
            'status' => $this->faker->randomElement(['pending', 'preparing', 'delivered', 'cancelled']),
            'special_instructions' => $this->faker->optional()->sentence(),
            'total_price' => $this->faker->randomFloat(2, 10, 200),
            'delivery_charge' => $this->faker->randomFloat(2, 0, 10),
            'expected_delivery_time' => $this->faker->time('H:i:s'),
            'actual_delivery_time' => $this->faker->optional()->time('H:i:s'),
        ];
    }
}
