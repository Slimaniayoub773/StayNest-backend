<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BlogFactory extends Factory
{
    public function definition(): array
    {
        // Define realistic categories with sample titles and content templates
        $blogs = [
            'Travel Tips' => [
                [
                    'title' => 'Top 10 Travel Tips for a Stress-Free Vacation',
                    'content' => 'Traveling can be fun and exciting, but planning is key. In this post, we share top tips to ensure a smooth journey, including packing strategies, choosing accommodations wisely, and staying safe while exploring new destinations.'
                ],
                [
                    'title' => 'How to Pack Light for Long Trips',
                    'content' => 'Packing light is essential for any traveler. Learn how to choose versatile clothing, organize efficiently, and travel without unnecessary baggage.'
                ],
            ],
            'Hotel Services' => [
                [
                    'title' => 'Maximizing Your Stay with Our Hotel Services',
                    'content' => 'Discover all the services our hotel offers, from concierge assistance to room service. Learn how to make the most of your stay and enjoy a luxurious experience.'
                ],
                [
                    'title' => 'Why Our Housekeeping Makes a Difference',
                    'content' => 'Cleanliness is our top priority. This post explores how our housekeeping staff ensures comfort and hygiene throughout your stay.'
                ],
            ],
            'Food & Dining' => [
                [
                    'title' => '5 Must-Try Dishes at Our Hotel Restaurant',
                    'content' => 'Indulge in our chef’s signature dishes. From freshly baked pastries to gourmet dinners, explore our menu and satisfy your cravings.'
                ],
                [
                    'title' => 'The Art of Hotel Breakfasts',
                    'content' => 'A great day starts with a great breakfast. Learn about our breakfast offerings and how we bring fresh and delicious meals to our guests every morning.'
                ],
            ],
            'Events' => [
                [
                    'title' => 'Hosting the Perfect Conference at Our Hotel',
                    'content' => 'Our hotel offers state-of-the-art conference rooms and professional staff to ensure your events run smoothly. Discover tips to plan and host successful meetings and gatherings.'
                ],
                [
                    'title' => 'Weddings and Celebrations: Making Memories',
                    'content' => 'Celebrate life’s special moments with our hotel’s elegant event spaces, catering, and services tailored to create unforgettable memories.'
                ],
            ],
            'Wellness' => [
                [
                    'title' => 'Relax and Rejuvenate at Our Spa',
                    'content' => 'Take time for yourself with our wellness and spa treatments. From massages to facials, discover how we help our guests unwind and refresh.'
                ],
                [
                    'title' => 'Staying Fit While Traveling',
                    'content' => 'Even on vacation, staying active is easy. Learn how our hotel gym, pool, and fitness programs help you maintain your routine while enjoying your stay.'
                ],
            ],
        ];

        // Pick a random category
        $category = $this->faker->randomElement(array_keys($blogs));

        // Pick a random blog for that category
        $blog = $blogs[$category][array_rand($blogs[$category])];

        // Random author
        $authors = ['John Smith', 'Emily Johnson', 'Michael Brown', 'Sophia Davis', 'David Wilson'];
        $author = $this->faker->randomElement($authors);

        return [
            'title' => $blog['title'],
            'content' => $blog['content'],
            'image' => 'blog-' . $this->faker->numberBetween(1, 5) . '.jpg',
            'category' => $category,
            'author' => $author,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
