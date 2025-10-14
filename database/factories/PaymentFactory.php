<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Booking;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        $statuses = ['pending', 'completed', 'failed', 'refunded'];

        return [
            'booking_id' => Booking::inRandomOrder()->value('id'),
            'payment_method' => $this->faker->randomElement(['credit_card', 'paypal', 'bank_transfer', 'cash']),
            'amount' => $this->faker->randomFloat(2, 50, 1000),
            'payment_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'status' => $this->faker->randomElement($statuses),
            'receipt_url' => $this->faker->optional()->url,
            'transaction_id' => $this->faker->optional()->uuid,
        ];
    }
}
