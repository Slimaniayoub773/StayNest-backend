<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
         $this->call([
        RoleSeeder::class,
        
        PermissionSeeder::class,
        RolePermissionSeeder::class,
        UserSeeder::class,
        GuestSeeder::class,
        RoomTypeSeeder::class,
         RoomSeeder::class,
         AmenitySeeder::class,
         RoomAmenitySeeder::class,
          OfferSeeder::class,
           RoomOfferSeeder::class,
           BookingSeeder::class,
            PaymentSeeder::class,   
             CleaningScheduleSeeder::class,
            RoomServiceCategorySeeder::class,
        RoomServiceItemSeeder::class,
        RoomServiceOrderSeeder::class,
        RoomServiceOrderItemSeeder::class,
              ReviewSeeder::class,
              HomePageSeeder::class,
            BlogSeeder::class,
  LegalMentionSeeder::class,
    FaqSeeder::class,
    ]);
    }
}
