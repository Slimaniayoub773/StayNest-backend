<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Amenity;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            ['name' => 'Free Wi-Fi', 'description' => 'High-speed internet access.', 'icon_class' => 'fa-solid fa-wifi'],
            ['name' => 'Swimming Pool', 'description' => 'Outdoor or indoor pool.', 'icon_class' => 'fa-solid fa-person-swimming'],
            ['name' => 'Gym', 'description' => 'Fully equipped fitness center.', 'icon_class' => 'fa-solid fa-dumbbell'],
            ['name' => 'Spa', 'description' => 'Relaxing treatments and massages.', 'icon_class' => 'fa-solid fa-spa'],
            ['name' => 'Parking', 'description' => 'Free parking available.', 'icon_class' => 'fa-solid fa-square-parking'],
            ['name' => 'Air Conditioning', 'description' => 'Climate control in every room.', 'icon_class' => 'fa-solid fa-wind'],
            ['name' => 'Restaurant', 'description' => 'On-site dining options.', 'icon_class' => 'fa-solid fa-utensils'],
            ['name' => 'Bar', 'description' => 'Cocktails and drinks available.', 'icon_class' => 'fa-solid fa-martini-glass'],
            ['name' => 'Room Service', 'description' => '24/7 in-room dining.', 'icon_class' => 'fa-solid fa-concierge-bell'],
            ['name' => 'Pet Friendly', 'description' => 'Pets are welcome.', 'icon_class' => 'fa-solid fa-dog'],
            ['name' => 'Business Center', 'description' => 'Meeting and work facilities.', 'icon_class' => 'fa-solid fa-briefcase'],
            ['name' => 'Laundry Service', 'description' => 'Professional cleaning service.', 'icon_class' => 'fa-solid fa-soap'],
            ['name' => 'TV', 'description' => 'Flat screen with satellite channels.', 'icon_class' => 'fa-solid fa-tv'],
            ['name' => 'Mini Bar', 'description' => 'Stocked mini fridge.', 'icon_class' => 'fa-solid fa-wine-bottle'],
            ['name' => 'Breakfast Included', 'description' => 'Complimentary breakfast.', 'icon_class' => 'fa-solid fa-bread-slice'],
        ];

        foreach ($amenities as $amenity) {
            Amenity::create($amenity);
        }
    }
}
