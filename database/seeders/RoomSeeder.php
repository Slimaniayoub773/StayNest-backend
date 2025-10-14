<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\RoomType;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        // Get RoomType IDs
        $single = RoomType::firstWhere('name', 'Single Room')?->id;
        $double = RoomType::firstWhere('name', 'Double Room')?->id;
        $twin = RoomType::firstWhere('name', 'Twin Room')?->id;
        $deluxe = RoomType::firstWhere('name', 'Deluxe Room')?->id;
        $suite = RoomType::firstWhere('name', 'Suite')?->id;
        $family = RoomType::firstWhere('name', 'Family Room')?->id;
        $presidential = RoomType::firstWhere('name', 'Presidential Suite')?->id;
        $studio = RoomType::firstWhere('name', 'Studio')?->id;
        $king = RoomType::firstWhere('name', 'King Room')?->id;
        $queen = RoomType::firstWhere('name', 'Queen Room')?->id;

        $rooms = [
            ['room_number' => 101, 'type_id' => $single, 'floor_number' => 1, 'price_per_night' => 80, 'status' => 'available'],
            ['room_number' => 102, 'type_id' => $single, 'floor_number' => 1, 'price_per_night' => 80, 'status' => 'booked'],
            ['room_number' => 103, 'type_id' => $double, 'floor_number' => 1, 'price_per_night' => 120, 'status' => 'available'],
            ['room_number' => 104, 'type_id' => $double, 'floor_number' => 1, 'price_per_night' => 120, 'status' => 'maintenance'],
            ['room_number' => 201, 'type_id' => $twin, 'floor_number' => 2, 'price_per_night' => 110, 'status' => 'available'],
            ['room_number' => 202, 'type_id' => $twin, 'floor_number' => 2, 'price_per_night' => 110, 'status' => 'booked'],
            ['room_number' => 203, 'type_id' => $deluxe, 'floor_number' => 2, 'price_per_night' => 200, 'status' => 'available'],
            ['room_number' => 204, 'type_id' => $deluxe, 'floor_number' => 2, 'price_per_night' => 200, 'status' => 'booked'],
            ['room_number' => 301, 'type_id' => $suite, 'floor_number' => 3, 'price_per_night' => 350, 'status' => 'available'],
            ['room_number' => 302, 'type_id' => $suite, 'floor_number' => 3, 'price_per_night' => 350, 'status' => 'maintenance'],
            ['room_number' => 303, 'type_id' => $family, 'floor_number' => 3, 'price_per_night' => 250, 'status' => 'available'],
            ['room_number' => 304, 'type_id' => $family, 'floor_number' => 3, 'price_per_night' => 250, 'status' => 'booked'],
            ['room_number' => 401, 'type_id' => $presidential, 'floor_number' => 4, 'price_per_night' => 1000, 'status' => 'available'],
            ['room_number' => 402, 'type_id' => $studio, 'floor_number' => 4, 'price_per_night' => 100, 'status' => 'available'],
            ['room_number' => 403, 'type_id' => $king, 'floor_number' => 4, 'price_per_night' => 180, 'status' => 'booked'],
            ['room_number' => 404, 'type_id' => $queen, 'floor_number' => 4, 'price_per_night' => 150, 'status' => 'available'],
            ['room_number' => 501, 'type_id' => $single, 'floor_number' => 5, 'price_per_night' => 80, 'status' => 'booked'],
            ['room_number' => 502, 'type_id' => $double, 'floor_number' => 5, 'price_per_night' => 120, 'status' => 'available'],
            ['room_number' => 503, 'type_id' => $twin, 'floor_number' => 5, 'price_per_night' => 110, 'status' => 'maintenance'],
            ['room_number' => 504, 'type_id' => $deluxe, 'floor_number' => 5, 'price_per_night' => 200, 'status' => 'available'],
            ['room_number' => 601, 'type_id' => $suite, 'floor_number' => 6, 'price_per_night' => 350, 'status' => 'booked'],
            ['room_number' => 602, 'type_id' => $family, 'floor_number' => 6, 'price_per_night' => 250, 'status' => 'available'],
            ['room_number' => 603, 'type_id' => $presidential, 'floor_number' => 6, 'price_per_night' => 1000, 'status' => 'available'],
            ['room_number' => 604, 'type_id' => $studio, 'floor_number' => 6, 'price_per_night' => 100, 'status' => 'booked'],
            ['room_number' => 701, 'type_id' => $king, 'floor_number' => 7, 'price_per_night' => 180, 'status' => 'available'],
            ['room_number' => 702, 'type_id' => $queen, 'floor_number' => 7, 'price_per_night' => 150, 'status' => 'booked'],
            ['room_number' => 703, 'type_id' => $single, 'floor_number' => 7, 'price_per_night' => 80, 'status' => 'available'],
            ['room_number' => 704, 'type_id' => $double, 'floor_number' => 7, 'price_per_night' => 120, 'status' => 'maintenance'],
            ['room_number' => 801, 'type_id' => $twin, 'floor_number' => 8, 'price_per_night' => 110, 'status' => 'available'],
            ['room_number' => 802, 'type_id' => $deluxe, 'floor_number' => 8, 'price_per_night' => 200, 'status' => 'booked'],
        ];

        foreach ($rooms as $room) {
            Room::updateOrCreate(['room_number' => $room['room_number']], $room);
        }
    }
}
