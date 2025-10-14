<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index()
{
    $roles = Role::withCount('permissions')->get();
    return response()->json($roles);
}
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|string|min:3|max:255|unique:roles',
            'description' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role = Role::create($validator->validated());
        return response()->json(['data' => $role, 'message' => 'Role created successfully'], 201);
    }

    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|string|min:3|max:255|unique:roles,role_name,' . $role->id,
            'description' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role->update($validator->validated());
        return response()->json(['data' => $role, 'message' => 'Role updated successfully'], 200);
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json(['message' => 'Role deleted successfully'], 200);
    }
}