<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'view_dashboard', 'description' => 'Access the main admin dashboard.'],
            ['name' => 'manage_reservations', 'description' => 'Create, edit, and cancel reservations.'],
            ['name' => 'check_in_guests', 'description' => 'Process guest check-ins.'],
            ['name' => 'check_out_guests', 'description' => 'Process guest check-outs.'],
            ['name' => 'assign_rooms', 'description' => 'Assign rooms to guests.'],
            ['name' => 'view_room_status', 'description' => 'View the status of all rooms.'],
            ['name' => 'manage_rooms', 'description' => 'Add, update, or delete rooms.'],
            ['name' => 'access_financial_reports', 'description' => 'View hotel financial summaries.'],
            ['name' => 'process_payments', 'description' => 'Handle guest payments and invoices.'],
            ['name' => 'manage_staff', 'description' => 'Hire, update, or remove staff profiles.'],
            ['name' => 'view_guest_profiles', 'description' => 'Access guest profile details.'],
            ['name' => 'send_notifications', 'description' => 'Send emails or SMS to guests.'],
            ['name' => 'manage_services', 'description' => 'Add/edit hotel services (spa, dining, etc.).'],
            ['name' => 'access_inventory', 'description' => 'View and manage hotel inventory.'],
            ['name' => 'schedule_maintenance', 'description' => 'Create maintenance schedules.'],
            ['name' => 'manage_permissions', 'description' => 'Create/edit system permissions.'],
            ['name' => 'manage_roles', 'description' => 'Assign roles and modify access rights.'],
            ['name' => 'view_logs', 'description' => 'Access activity logs and audit trails.'],
            ['name' => 'handle_complaints', 'description' => 'Respond to and resolve guest complaints.'],
            ['name' => 'generate_reports', 'description' => 'Create operational or performance reports.'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission['name']], $permission);
        }
    }
}
