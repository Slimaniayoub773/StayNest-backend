<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HomePageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('home_pages')->insert([
            'name' => 'StayNest',
            'logo' => 'home-page/trans_bg.png', // مسار الصورة داخل storage/app/public
            'address' => '25 Mars Bloc W N 41',
            'city' => 'Laayoune',
            'phone' => '+212 636847568',
            'email' => 'ayoubslimani773@gmail.com',
            'description' => 'StayNest Hotel offers luxurious comfort and modern amenities in the heart of Laayoune. Perfect for both business and leisure travelers, enjoy our welcoming atmosphere, excellent service, and convenient location.',
            'map' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d113616.27061100774!2d-13.26593443511089!3d27.140288096172963!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xc37731e21ffd02f%3A0xb5d8ba3b30a4a46b!2sH%C3%B4tel%20Al%20Massira!5e0!3m2!1sen!2s!4v1759001672892!5m2!1sen!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade',
            'facebook' => 'https://www.facebook.com/profile.php?id=100089092542253',
            'instagram' => 'https://www.instagram.com/ayoub___sli773/',
            'whatsapp' => 'https://wa.me/+212636847568',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
