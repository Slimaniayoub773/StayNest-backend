<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RoomServiceCategory;

class RoomServiceItemFactory extends Factory
{
    public function definition(): array
    {
        $category = RoomServiceCategory::inRandomOrder()->first();

        // Define realistic items per category
        $items = [
            'Food Service' => [
                ['name_ar' => 'شاي', 'name_en' => 'Tea', 'price' => 5, 'preparation_time' => 5],
                ['name_ar' => 'قهوة', 'name_en' => 'Coffee', 'price' => 6, 'preparation_time' => 5],
                ['name_ar' => 'بيتزا', 'name_en' => 'Pizza', 'price' => 20, 'preparation_time' => 15],
                ['name_ar' => 'عصير', 'name_en' => 'Juice', 'price' => 8, 'preparation_time' => 7],
                ['name_ar' => 'سلطة', 'name_en' => 'Salad', 'price' => 10, 'preparation_time' => 10],
            ],
            'Housekeeping' => [
                ['name_ar' => 'مناشف إضافية', 'name_en' => 'Extra Towels', 'price' => 2, 'preparation_time' => 2],
                ['name_ar' => 'تغيير أغطية السرير', 'name_en' => 'Change Bed Linens', 'price' => 10, 'preparation_time' => 15],
                ['name_ar' => 'تنظيف الغرفة', 'name_en' => 'Room Cleaning', 'price' => 15, 'preparation_time' => 20],
            ],
            'Laundry' => [
                ['name_ar' => 'غسيل الملابس', 'name_en' => 'Clothes Laundry', 'price' => 12, 'preparation_time' => 60],
                ['name_ar' => 'كي الملابس', 'name_en' => 'Clothes Ironing', 'price' => 8, 'preparation_time' => 30],
            ],
            'Spa' => [
                ['name_ar' => 'تدليك الجسم', 'name_en' => 'Body Massage', 'price' => 50, 'preparation_time' => 60],
                ['name_ar' => 'عناية الوجه', 'name_en' => 'Facial Care', 'price' => 40, 'preparation_time' => 45],
            ],
            'Maintenance' => [
                ['name_ar' => 'إصلاح السباكة', 'name_en' => 'Plumbing Repair', 'price' => 30, 'preparation_time' => 120],
                ['name_ar' => 'إصلاح الكهرباء', 'name_en' => 'Electrical Repair', 'price' => 35, 'preparation_time' => 90],
            ],
        ];

        // Pick a random item from the category
        $item = $items[$category->name_en][array_rand($items[$category->name_en])];

        return [
            'category_id' => $category->id,
            'name_ar' => $item['name_ar'],
            'name_en' => $item['name_en'],
            'description' => $this->faker->sentence(6),
            'price' => $item['price'],
            'preparation_time' => $item['preparation_time'],
            'is_available' => $this->faker->boolean(90),
            'image_url' => $category->is_food ? $this->faker->imageUrl(640, 480, 'food', true) : null,
        ];
    }
}
