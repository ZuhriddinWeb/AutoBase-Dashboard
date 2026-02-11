<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // API so'rov uchun
use Illuminate\Support\Facades\DB;   // Baza uchun
use App\Models\Machine;
use App\Models\TransportGroup;
use App\Models\GeozoneGroup;
use App\Models\Page;
use App\Models\SystemLog;
use App\Models\WialonSetting; // Token bor jadvalingiz

class DashboardController extends Controller
{
    public function stats()
    {
        // 1. STATISTIKA
        $groups = TransportGroup::pluck('name');
        $totalMachines = Machine::count();
        $chartSeries = [];

        if ($groups->count() > 0) {
            $avg = floor($totalMachines / $groups->count());
            foreach ($groups as $index => $group) {
                $chartSeries[] = $avg + rand(-50, 50);
            }
        }

        // 2. WIALON HOLATINI TEKSHIRISH
        $wialonStatus = $this->checkWialonStatus();

        // 3. DATABASE HOLATINI TEKSHIRISH
        $dbStatus = $this->checkDatabaseStatus();

        return response()->json([
            // Asosiy raqamlar
            'machines_count' => $totalMachines,
            'transport_groups_count' => TransportGroup::count(),
            'geozone_groups_count' => GeozoneGroup::count(),
            'pages_count' => Page::count(),

            'latest_machines' => Machine::latest()->take(5)->get(['id', 'name', 'created_at']),

            // Loglar
            'system_logs' => SystemLog::latest()
                ->take(5)
                ->get()
                ->map(function ($log) {
                    return [
                        'user' => $log->user_name,
                        'action' => $log->action,
                        'time' => $log->created_at->format('H:i d.m'),
                        'status' => $log->status
                    ];
                }),

            // Grafik
            'chart_data' => [
                'labels' => $groups,
                'series' => $chartSeries
            ],

            // --- YANGI: REAL STATUSLAR ---
            'wialon_status' => $wialonStatus,
            'db_status' => $dbStatus
        ]);
    }

    // Wialonni tekshirish funksiyasi
    private function checkWialonStatus()
    {
        $start = microtime(true);
        $isOnline = false;
        $ping = 0;

        try {
            $start = microtime(true);

            // 1. Bazadan sozlamalarni olamiz
            $setting = WialonSetting::where('status', 'active')->first();

            // Agar sozlamalar yo'q bo'lsa, tekshirib o'tirmaymiz
            if (!$setting || empty($setting->base_url)) {
                throw new \Exception('Wialon sozlamalari topilmadi');
            }

            // 2. URLni to'g'irlash (oxiridagi "/" belgisini olib tashlash va to'g'ri yo'lni qo'shish)
            // Masalan: https://wl.ngmk.uz -> https://wl.ngmk.uz/wialon/ajax.html
            $baseUrl = rtrim($setting->base_url, '/');
            $apiUrl = $baseUrl . '/wialon/ajax.html';

            // 3. Aynan O'ZINGIZNING serveringizga so'rov yuboramiz
            $response = Http::timeout(2)->get($apiUrl, [
                'svc' => 'token/login',
                'params' => json_encode(['token' => $setting->token])
            ]);

            $end = microtime(true);
            $ping = round(($end - $start) * 1000);

            // Javobni tekshirish (faqat 200 OK bo'lishi yetarli emas, error kodi bo'lmasligi kerak)
            if ($response->successful() && !isset($response->json()['error'])) {
                $isOnline = true;
            } else {
                $isOnline = false;
            }

        } catch (\Exception $e) {
            $isOnline = false;
            $ping = 0;
            // Log::error("Wialon ulanish xatosi: " . $e->getMessage()); // Ixtiyoriy: logga yozish
        }
        return [
            'online' => $isOnline,
            'ping' => $ping,
            'text' => $isOnline ? 'ONLINE' : 'OFFLINE',
            'color' => $isOnline ? 'text-green-400' : 'text-red-500'
        ];
    }

    // Bazani tekshirish funksiyasi (SQL Server uchun)
    private function checkDatabaseStatus()
    {
        try {
            // Baza hajmini o'lchash (SQL Server Query)
            // Bu query bazaning hajmini MB da qaytaradi
            $size = DB::select("SELECT sum(size) * 8 / 1024 AS SizeMB FROM sys.master_files WHERE database_id = DB_ID()")[0]->SizeMB;

            // Taxminiy to'lganlik foizi (Masalan, 10GB limit deb olsak)
            // O'zingiz xohlagan limitni qo'yishingiz mumkin (hozir 10240 MB = 10GB)
            $limit = 10240;
            $percent = round(($size / $limit) * 100);

            return [
                'online' => true,
                'size_mb' => round($size, 1),
                'percent' => $percent > 100 ? 100 : $percent, // 100 dan oshib ketmasin
                'text' => 'SQL SERVER'
            ];
        } catch (\Exception $e) {
            return [
                'online' => false,
                'size_mb' => 0,
                'percent' => 0,
                'text' => 'ULANISH YO\'Q'
            ];
        }
    }
}