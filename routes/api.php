<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UniversalController; // Api (katta harf bilan bo'lishi tavsiya etiladi)
use App\Http\Controllers\Api\WialonController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\SystemLogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\GeozoneController;

// --- AUTH ---
Route::post('/login', [AuthController::class, 'login']);
Route::get('/wialon-explore', [WialonController::class, 'exploreData']);
Route::middleware('auth:sanctum')->group(function () {
     Route::get('/roles', [RoleController::class, 'index']);           // Ro'yxatni olish
    Route::post('/roles', [RoleController::class, 'store']);          // Yangi rol (+ Yangi Rol tugmasi uchun)
    Route::delete('/roles/{id}', [RoleController::class, 'destroy']); // Rolni o'chirish
    
    // 2. Ruxsatlar (Permissions)
    Route::get('/permissions', [RoleController::class, 'getAllPermissions']); // Hamma ruxsatlar (Jadval uchun)
    Route::get('/roles/{id}/permissions', [RoleController::class, 'getRolePermissions']); // Rol ruxsatlari
    Route::post('/roles/{id}/sync-permissions', [RoleController::class, 'syncPermissions']); // Saqlash tugmasi uchun
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/users-list', [DashboardController::class, 'getUsers']); // <--- YANGI
    Route::get('/dashboard/config', [DashboardController::class, 'getConfig']);
    Route::post('/dashboard/config', [DashboardController::class, 'saveConfig']);
    Route::get('/dashboard/data', [WialonController::class, 'getDashboardData']);
    Route::post('/wialon/sync-geozones', [WialonController::class, 'syncGeozones']);
    
});
Route::apiResource('crud/geozones', GeozoneController::class);
Route::get('/wialon-test', [WialonController::class, 'testWialonData']);
// --- DASHBOARD & LOGS ---
Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
Route::get('/system-logs', [SystemLogController::class, 'index']);

// --- WIALON (MAXSUS MARSHRUTLAR - TEPADA TURISHI SHART) ---
// Bu yerda biz aniq WialonController ga yo'naltiryapmiz
Route::get('/wialon/check', [WialonController::class, 'checkConnection']);
Route::post('/crud/wialon_settings', [WialonController::class, 'store']);
Route::post('/crud/wialon', [WialonController::class, 'store']);       // Ehtiyot shart ikkalasini ham qoldiramiz
// Wialon bo'limiga qo'shing:
Route::post('/wialon/sync-machines', [WialonController::class, 'syncMachines']);
Route::post('/wialon/sync-groups', [WialonController::class, 'syncGroups']);
// --- PAGES (MAXSUS) ---
Route::apiResource('crud/pages', PageController::class);

// --- UNIVERSAL CRUD (ENG PASTDA) ---
// Boshqa barcha 'crud/...' so'rovlari shu yerga tushadi
Route::prefix('crud')->group(function () {
    Route::get('{resource}', [UniversalController::class, 'index']);
    Route::post('{resource}', [UniversalController::class, 'store']);
    Route::put('{resource}/{id}', [UniversalController::class, 'update']);
    Route::delete('{resource}/{id}', [UniversalController::class, 'destroy']);
});