<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LegalMention;

class LegalMentionSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء 5 سجلات قانونية باستخدام الـ Factory
        LegalMention::factory()->count(5)->create();
    }
}
