<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoomType;

class RoomTypeSeeder extends Seeder
{
    public function run(): void
    {
        $roomTypes = [
            ['name' => 'Single Room', 'description' => 'Ideal for solo travelers.', 'base_price' => 80, 'max_occupancy' => 1],
            ['name' => 'Double Room', 'description' => 'Comfortable for two guests.', 'base_price' => 120, 'max_occupancy' => 2],
            ['name' => 'Twin Room', 'description' => 'Two single beds, perfect for companions.', 'base_price' => 110, 'max_occupancy' => 2],
            ['name' => 'Deluxe Room', 'description' => 'Larger space with premium features.', 'base_price' => 200, 'max_occupancy' => 2],
            ['name' => 'Suite', 'description' => 'Luxurious suite with a living area.', 'base_price' => 350, 'max_occupancy' => 3],
            ['name' => 'Family Room', 'description' => 'Spacious room for the whole family.', 'base_price' => 250, 'max_occupancy' => 4],
            ['name' => 'Presidential Suite', 'description' => 'Top-tier luxury and amenities.', 'base_price' => 1000, 'max_occupancy' => 6],
            ['name' => 'Studio', 'description' => 'Compact yet comfortable with kitchenette.', 'base_price' => 100, 'max_occupancy' => 2],
            ['name' => 'King Room', 'description' => 'King-sized bed and premium amenities.', 'base_price' => 180, 'max_occupancy' => 2],
            ['name' => 'Queen Room', 'description' => 'Queen-sized bed and modern decor.', 'base_price' => 150, 'max_occupancy' => 2],
        ];

        foreach ($roomTypes as $type) {
            RoomType::updateOrCreate(['name' => $type['name']], $type);
        }
    }
}
