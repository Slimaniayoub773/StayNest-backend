<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoomServiceItem;
use App\Models\RoomServiceCategory;

class RoomServiceItemSeeder extends Seeder
{
    public function run(): void
    {
        $categories = RoomServiceCategory::all();

        foreach ($categories as $category) {
            // Create 5 realistic items per category
            RoomServiceItem::factory()->count(5)->create([
                'category_id' => $category->id,
            ]);
        }
    }
}
