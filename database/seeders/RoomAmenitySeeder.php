<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\Amenity;
use Illuminate\Database\Seeder;

class RoomAmenitySeeder extends Seeder
{
    public function run(): void
    {
        $amenityIds = Amenity::pluck('id')->toArray();

        // Loop over each room
        Room::all()->each(function ($room) use ($amenityIds) {
            // Randomly assign 3 to 7 amenities per room
            $assigned = collect($amenityIds)->random(rand(3, 7))->all();
            $room->amenities()->sync($assigned);
        });
    }
}
