<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\BookingStatusHistory;

class BookingStatusHistorySeeder extends Seeder
{
    public function run(): void
    {
        $bookings = Booking::all();

        foreach ($bookings as $booking) {
            $statuses = ['pending', 'confirmed', 'checked_in', 'checked_out'];
            $historyCount = rand(1, 4);

            for ($i = 0; $i < $historyCount; $i++) {
                BookingStatusHistory::create([
                    'booking_id' => $booking->id,
                    'status' => $statuses[$i],
                    'changed_at' => now()->subDays(rand(5, 20)),
                    'changed_by' => 'System', // Or use $faker->name
                    'notes' => 'Auto status update.',
                ]);
            }
        }
    }
}
