<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['role_name' => 'Admin', 'description' => 'Oversees all hotel operations and manages staff.'],
            ['role_name' => 'Front Desk Receptionist', 'description' => 'Manages guest check-ins, check-outs, and reservations.'],
            ['role_name' => 'Housekeeper', 'description' => 'Maintains cleanliness and hygiene of rooms and public areas.'],
            ['role_name' => 'Cleaner', 'description' => 'Performs general cleaning and maintenance tasks throughout the hotel.'],
            ['role_name' => 'Concierge', 'description' => 'Assists guests with information, bookings, and recommendations.'],
            ['role_name' => 'Bellhop', 'description' => 'Handles luggage and assists guests upon arrival and departure.'],
            ['role_name' => 'Room Service Staff', 'description' => 'Delivers meals and amenities to guest rooms.'],
            ['role_name' => 'Chef', 'description' => 'Prepares meals and supervises kitchen staff.'],
            ['role_name' => 'Sous Chef', 'description' => 'Assists head chef in meal preparation and kitchen management.'],
            ['role_name' => 'Line Cook', 'description' => 'Responsible for preparing specific sections of menu items.'],
            ['role_name' => 'Waiter/Waitress', 'description' => 'Serves food and drinks to guests in dining areas.'],
            ['role_name' => 'Bartender', 'description' => 'Prepares and serves beverages at the bar.'],
            ['role_name' => 'Security Officer', 'description' => 'Ensures the safety and security of guests and hotel property.'],
            ['role_name' => 'Maintenance Technician', 'description' => 'Performs repairs and upkeep of hotel facilities.'],
            ['role_name' => 'Laundry Attendant', 'description' => 'Manages hotel linens and laundry services.'],
            ['role_name' => 'Event Coordinator', 'description' => 'Plans and organizes conferences, events, and banquets.'],
            ['role_name' => 'Spa Therapist', 'description' => 'Provides wellness treatments and spa services to guests.'],
            ['role_name' => 'Valet Parking Attendant', 'description' => 'Parks and retrieves guest vehicles efficiently.'],
            ['role_name' => 'IT Support', 'description' => 'Maintains hotel IT infrastructure and resolves technical issues.'],
            ['role_name' => 'HR Manager', 'description' => 'Manages recruitment, training, and employee relations.'],
            ['role_name' => 'Finance Manager', 'description' => 'Oversees budgeting, accounting, and financial operations.'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['role_name' => $role['role_name']], $role);
        }
    }
}
