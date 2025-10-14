<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\Offer;

class RoomOfferSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we have rooms and offers
        if (Room::count() === 0 || Offer::count() === 0) {
            $this->command->warn('No rooms or offers found. Please seed them first.');
            return;
        }

        $rooms = Room::all();
        $offers = Offer::all();

        foreach ($rooms as $room) {
            // Attach 1–3 random offers to each room
            $room->offers()->attach(
                $offers->random(rand(1, 3))->pluck('id')->toArray()
            );
        }
    }
}
