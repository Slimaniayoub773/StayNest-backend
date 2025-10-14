<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guest;

class GuestSeeder extends Seeder
{
    public function run(): void
    {
        // Generate 20 guests
        Guest::factory()->count(20)->create();
    }
}
