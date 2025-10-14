<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء 10 FAQs باستخدام الـ Factory
        Faq::factory()->count(10)->create();
    }
}
