<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoomServiceOrder;

class RoomServiceOrderSeeder extends Seeder
{
    public function run(): void
    {
        RoomServiceOrder::factory()->count(25)->create();
    }
}
