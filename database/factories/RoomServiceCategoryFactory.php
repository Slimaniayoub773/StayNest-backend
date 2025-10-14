<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoomServiceCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name_ar' => $this->faker->randomElement(['خدمة الطعام', 'تنظيف الغرف', 'الصيانة', 'خدمة الغسيل', 'السبا']),
            'name_en' => $this->faker->randomElement(['Food Service', 'Housekeeping', 'Maintenance', 'Laundry', 'Spa']),
            'description' => $this->faker->optional()->sentence,
            'is_food' => $this->faker->boolean(70), // 70% likely to be food-related
            'available_hours' => $this->faker->optional()->regexify('[0-9]{2}:[0-9]{2} - [0-9]{2}:[0-9]{2}'),
        ];
    }
}
