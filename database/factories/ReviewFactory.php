<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Guest;
use App\Models\Room;
use App\Models\Booking;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'guest_id' => Guest::inRandomOrder()->first()->id ?? Guest::factory(),
            'room_id' => Room::inRandomOrder()->first()->id ?? Room::factory(),
            'booking_id' => Booking::inRandomOrder()->first()->id ?? Booking::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->randomElement([
                'Great stay, very clean and comfortable.',
                'The staff was very friendly and helpful.',
                'Good location but the room was a bit small.',
                'Excellent service and delicious breakfast.',
                'Room was clean but could hear noise from outside.',
                'Loved the pool and spa, will come back again!',
                'The bed was super comfortable, had a great sleep.',
                'Wi-Fi was fast and reliable, perfect for work.',
                'Bathroom was modern and well maintained.',
                'Overall a pleasant experience, highly recommended.',
                'Average service, could be improved.',
                'Too expensive for the quality provided.',
                'Had some issues with the air conditioning.',
                'Check-in was smooth and quick.',
                'The view from the room was amazing!',
                'Not very clean, disappointed.',
                'Breakfast options were limited.',
                'Excellent value for money.',
                'Would definitely recommend this hotel.',
                'Had a wonderful family vacation here.'
            ]),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
