<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Guest;
use App\Models\Room;
use App\Models\Offer;

class BookingFactory extends Factory
{
    public function definition(): array
    {
        $checkIn = $this->faker->dateTimeBetween('-1 week', '+1 month');
        $checkOut = (clone $checkIn)->modify('+'.rand(1, 7).' days');
        $room = Room::inRandomOrder()->first();
        $pricePerNight = $room ? $room->price_per_night : 100;

        $nights = $checkIn->diff($checkOut)->days;
        $total = $pricePerNight * $nights;

        return [
            'user_id' => User::inRandomOrder()->value('id'),
            'guest_id' => Guest::inRandomOrder()->value('id'),
            'room_id' => $room?->id ?? Room::factory(),
            'offer_id' => Offer::inRandomOrder()->value('id'),
            'check_in_date' => $checkIn->format('Y-m-d'),
            'check_out_date' => $checkOut->format('Y-m-d'),
            'booking_status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled']),
            'total_price' => $total,
            'payment_status' => $this->faker->randomElement(['paid', 'unpaid', 'refunded']),
            'number_of_guests' => $this->faker->numberBetween(1, 4),
            'cancellation_policy' => $this->faker->optional()->sentence,
            'special_requests' => $this->faker->optional()->sentence,
        ];
    }
}
