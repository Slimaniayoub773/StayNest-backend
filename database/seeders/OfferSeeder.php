<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Offer;

class OfferSeeder extends Seeder
{
    public function run(): void
    {
        Offer::factory()->count(20)->create();
    }
}
