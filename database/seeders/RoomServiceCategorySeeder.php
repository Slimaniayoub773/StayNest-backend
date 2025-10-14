<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoomServiceCategory;

class RoomServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name_ar' => 'خدمة الطعام', 'name_en' => 'Food Service', 'is_food' => true, 'available_hours' => '08:00 - 23:00'],
            ['name_ar' => 'تنظيف الغرف', 'name_en' => 'Housekeeping', 'is_food' => false, 'available_hours' => '09:00 - 17:00'],
            ['name_ar' => 'الصيانة', 'name_en' => 'Maintenance', 'is_food' => false, 'available_hours' => '24/7'],
            ['name_ar' => 'خدمة الغسيل', 'name_en' => 'Laundry', 'is_food' => false, 'available_hours' => '10:00 - 20:00'],
            ['name_ar' => 'السبا', 'name_en' => 'Spa', 'is_food' => false, 'available_hours' => '12:00 - 22:00'],
        ];

        foreach ($categories as $category) {
            RoomServiceCategory::create($category);
        }

        // Optionally add a few random ones
        RoomServiceCategory::factory()->count(5)->create();
    }
}
