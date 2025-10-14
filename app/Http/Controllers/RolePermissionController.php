<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function getPermissions($roleId)
{
    $role = Role::withCount('permissions')->findOrFail($roleId);
    $permissions = Permission::all();
    
    return response()->json([
        'role' => $role,
        'allPermissions' => $permissions,
        'assignedPermissions' => $role->permissions->pluck('id')->toArray()
    ]);
}

    public function updatePermissions(Request $request, $roleId)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::findOrFail($roleId);
        $role->permissions()->sync($request->permissions);

        return response()->json([
            'message' => 'Permissions updated successfully',
            'role' => $role->load('permissions')
        ]);
    }
}