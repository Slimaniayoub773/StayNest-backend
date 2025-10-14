<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Faq>
 */
class FaqFactory extends Factory
{
    protected $model = \App\Models\Faq::class;

    public function definition(): array
    {
        // Realistic hotel FAQs
        $faqs = [
            [
                'question' => 'What are the check-in and check-out times?',
                'answer' => 'Guests can check in from 2 PM and check out until 12 PM. Different times can be arranged upon request.',
            ],
            [
                'question' => 'Is parking available for guests?',
                'answer' => 'Yes, we offer free parking for all guests, with valet service available upon request.',
            ],
            [
                'question' => 'Are pets allowed?',
                'answer' => 'Yes, small pets are allowed for an additional fee. Please notify the front desk in advance.',
            ],
            [
                'question' => 'Is Wi-Fi available at the hotel?',
                'answer' => 'Free Wi-Fi is available throughout the hotel, including rooms and public areas.',
            ],
            [
                'question' => 'Does the hotel offer room service?',
                'answer' => 'Yes, food and beverages can be ordered to your room throughout the day via room service.',
            ],
            [
                'question' => 'Are family rooms available?',
                'answer' => 'Yes, family rooms and suites are available to accommodate large families. Contact our reservations team for details.',
            ],
            [
                'question' => 'Is airport shuttle service available?',
                'answer' => 'Yes, airport shuttle service is available upon request with an additional fee.',
            ],
            [
                'question' => 'What payment methods are accepted?',
                'answer' => 'We accept cash, major credit cards, and some digital wallets.',
            ],
            [
                'question' => 'Are facilities available for guests with disabilities?',
                'answer' => 'Yes, the hotel is equipped with accessible facilities for guests with disabilities to ensure comfort and convenience.',
            ],
            [
                'question' => 'Does the hotel offer recreational activities?',
                'answer' => 'Yes, we offer a range of activities including sightseeing tours, children’s programs, and fitness classes.',
            ],
        ];

        // Pick a random FAQ
        $faq = $faqs[array_rand($faqs)];

        return [
            'question' => $faq['question'],
            'answer' => $faq['answer'],
            'order' => $this->faker->numberBetween(1, 10),
        ];
    }
}
