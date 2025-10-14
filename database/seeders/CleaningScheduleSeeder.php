<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CleaningSchedule;

class CleaningScheduleSeeder extends Seeder
{
    public function run(): void
    {
        CleaningSchedule::factory()->count(30)->create();
    }
}
