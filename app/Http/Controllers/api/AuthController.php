<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validatsiya
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        // 2. Login orqali user borligini tekshirish
        // (Sizda email emas, login ishlatilmoqda)
        $credentials = $request->only('login', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Login yoki parol noto‘g‘ri!'
            ], 401);
        }

        $user = Auth::user();
        
        // 3. Token yaratish
        $token = $user->createToken('auth_token')->plainTextToken;

        // 4. MUHIM: Rol va Ruxsatlarni olish
        // Spatie paketidan foydalanamiz
        $roles = $user->getRoleNames(); // ["Super Admin", "Manager"] kabi qaytadi
        $permissions = $user->getAllPermissions()->pluck('name'); // ["pages_create", "users_edit"]

        return response()->json([
            'message' => 'Xush kelibsiz, ' . $user->name,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'roles' => $roles,           // <--- BU JUDA MUHIM
            'permissions' => $permissions // <--- BU HAM
        ]);
    }

    public function logout(Request $request)
    {
        // Tokenni o'chirish
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Tizimdan chiqildi']);
    }
}