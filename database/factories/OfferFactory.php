<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OfferFactory extends Factory
{
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $endDate = (clone $startDate)->modify('+'.rand(3, 14).' days');

        // Define titles with matching realistic descriptions
        $offers = [
            'Summer Getaway' => 'Enjoy a relaxing summer stay with complimentary breakfast and pool access.',
            'Weekend Special' => 'Book your weekend stay and get a 20% discount on rooms and dining.',
            'Early Bird Discount' => 'Plan ahead and enjoy early booking discounts up to 30%.',
            'Romantic Escape' => 'Special package for couples including a candlelit dinner and spa treatment.',
            'Business Package' => 'Ideal for business travelers with free Wi-Fi and meeting room access.',
            'Family Fun Offer' => 'Perfect family stay with discounted rates for kids and fun activities included.',
            'Luxury Stay Deal' => 'Indulge in luxury with premium suite access and exclusive services.',
            'Spa & Stay Combo' => 'Relax with a spa treatment included in your hotel stay.',
            'City Explorer Discount' => 'Discover the city with our package including guided tours and transport.',
            'Winter Wonderland Package' => 'Experience a cozy winter stay with festive decorations and hot drinks.',
        ];

        $title = $this->faker->randomElement(array_keys($offers));
        $description = $offers[$title];

        return [
            'title' => $title,
            'description' => $description,
            'discount_percentage' => $this->faker->randomFloat(2, 5, 50),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'promo_code' => $this->faker->optional()->bothify('HOTEL-####'),
            'is_active' => $this->faker->boolean(80),
        ];
    }
}
