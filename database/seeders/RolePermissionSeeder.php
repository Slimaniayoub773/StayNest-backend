<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Clear the pivot table first (optional)
        DB::table('role_permissions')->truncate();

        $roles = Role::all();
        $permissions = Permission::all();

        foreach ($roles as $role) {
            // Assign specific permissions based on role
            switch ($role->role_name) {
                case 'Admin':
                    $rolePermissions = $permissions->pluck('id')->toArray(); // Admin gets all permissions
                    break;

                case 'Housekeeper':
                case 'Cleaner':
                    $rolePermissions = $permissions
                        ->whereIn('name', ['view_room_status', 'schedule_maintenance', 'handle_complaints'])
                        ->pluck('id')
                        ->toArray();
                    break;

                case 'Front Desk Receptionist':
                    $rolePermissions = $permissions
                        ->whereIn('name', ['view_dashboard', 'manage_reservations', 'check_in_guests', 'check_out_guests', 'assign_rooms'])
                        ->pluck('id')
                        ->toArray();
                    break;

                case 'Room Service Staff':
                    $rolePermissions = $permissions
                        ->whereIn('name', ['manage_services', 'send_notifications'])
                        ->pluck('id')
                        ->toArray();
                    break;

                default:
                    // For other roles, assign 5 random permissions
                    $rolePermissions = $permissions->random(5)->pluck('id')->toArray();
                    break;
            }

            foreach ($rolePermissions as $permissionId) {
                DB::table('role_permissions')->insert([
                    'role_id' => $role->id,
                    'permission_id' => $permissionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
