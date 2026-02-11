<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    // Barcha rollarni olish
    public function index()
    {
        return Role::withCount('permissions')->get();
    }

    // Yangi rol yaratish
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:roles,name']);
        
        $role = Role::create([
            'name' => $request->name, 
            'guard_name' => 'web'
        ]);
        
        return response()->json($role);
    }

    // Rolni o'chirish
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        if ($role->name === 'Super Admin') {
            return response()->json(['message' => 'Super Admin o\'chirilmaydi'], 403);
        }
        $role->delete();
        return response()->json(['message' => 'O\'chirildi']);
    }

    // Hamma ruxsatlarni olish
    public function getAllPermissions()
    {
        return Permission::all();
    }

    // Konkret rolning ruxsatlarini olish
    public function getRolePermissions($id)
    {
        $role = Role::findOrFail($id);
        return $role->permissions;
    }

    // Ruxsatlarni saqlash
    public function syncPermissions(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $role->syncPermissions($request->permissions); // ['users_create', 'pages_view']
        return response()->json(['message' => 'Saqlandi']);
    }
}