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
use App\Models\UserDashboard;
use Auth;
use App\Models\User;
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
    public function getConfig(Request $request)
    {
        // Agar requestda user_id kelsa, o'shanikini olamiz, bo'lmasa o'zimiznikini
        $targetUserId = $request->query('user_id', Auth::id());

        $dashboard = UserDashboard::firstOrCreate(
            ['user_id' => $targetUserId],
            ['layout' => []]
        );

        return response()->json($dashboard->layout);
    }
    public function saveConfig(Request $request)
    {
        $request->validate([
            'layout' => 'required|array',
            'user_id' => 'nullable|exists:users,id' // user_id bo'lsa tekshiramiz
        ]);

        // Agar user_id kelsa o'sha userga, kelmasa o'ziga saqlaydi
        $targetUserId = $request->input('user_id', Auth::id());

        $dashboard = UserDashboard::updateOrCreate(
            ['user_id' => $targetUserId],
            ['layout' => $request->layout]
        );

        return response()->json(['message' => 'Dashboard muvaffaqiyatli saqlandi!']);
    }
    public function getRealData()
    {
        // Izoh: Real loyihada bu yerda "Machine", "Fuel", "Event" kabi modellardan
        // ma'lumotlar olinadi. Hozircha vizual ko'rinish uchun
        // realistik ma'lumotlarni generatsiya qilamiz.

        $data = [
            // --- 1. STATISTIKA KARTALARI ---
            // Haqiqiy misol: DB::table('machines')->count()
            'TotalMachines' => 142,
            'ActiveDrivers' => 89,
            'FuelConsumption' => rand(4200, 4600) . 'L', // O'zgaruvchan
            'TotalDistance' => rand(12000, 12100) . 'km',
            'AlertsCount' => rand(3, 15),
            'OnlineUsers' => rand(2, 8),

            // --- 2. GRAFIKLAR (Array of values) ---
            // Frontenddagi sparkline chartlar uchun (15 ta nuqta)
            'WeeklyChart' => $this->generateChartData(15, 20, 100),
            'FuelChart' => $this->generateChartData(15, 40, 90),
            'DistanceChart' => $this->generateChartData(15, 100, 500),
            'SplineChart' => $this->generateChartData(20, 10, 50), // Signal sifati

            // --- 3. STATUS GRIDLARI (Eng muhim qism) ---
            // Bu yerda texnikalar ro'yxatini qaytaramiz
            'GreenGrid' => $this->generateGridData('C', 481, 32), // 32 ta faol texnika
            'OrangeGrid' => $this->generateGridData('C', 520, 16), // 16 ta yoqilg'ida
            'BlueGrid' => $this->generateGridData('P', 101, 24), // 24 ta parkovkada

            // --- 4. GAUGE (SPIDOMETR VA RPM) ---
            'SpeedGauge' => rand(0, 180), // Km/h
            'RpmGauge' => rand(10, 80) / 10, // Masalan: 2.4 x1000

            // --- 5. TIZIM STATUSLARI ---
            'ServerStatus' => rand(20, 60), // %
            'DbStatus' => 78, // % (Stabil)
            'WialonStatus' => rand(10, 50), // Ping (ms)

            // --- 6. RO'YXATLAR (LISTS) ---
            'LastEvents' => [
                ['name' => 'C450 - Tezlik oshirdi', 'status' => 'ERR'],
                ['name' => 'C421 - Zona tark etdi', 'status' => 'ERR'],
                ['name' => 'C102 - Ish boshladi', 'status' => 'OK'],
                ['name' => 'C305 - Yoqilg\'i quyildi', 'status' => 'OK'],
                ['name' => 'C200 - Signal yo\'q', 'status' => 'ERR'],
            ],
            'TopDrivers' => [
                ['name' => 'Aliyev V.', 'status' => '98%'],
                ['name' => 'Valiyev S.', 'status' => '95%'],
                ['name' => 'Karimov A.', 'status' => '92%'],
            ],
            'MaintenanceList' => [
                ['name' => 'Ekskavator #4', 'status' => 'Moy'],
                ['name' => 'KamAZ #12', 'status' => 'Shina'],
            ]
        ];

        return response()->json($data);
    }
    private function generateChartData($count, $min, $max)
    {
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $data[] = rand($min, $max);
        }
        return $data;
    }
    private function generateGridData($prefix, $startId, $count)
    {
        $items = [];
        for ($i = 0; $i < $count; $i++) {
            $items[] = [
                'id' => $i,
                'label' => $prefix . ($startId + $i), // Masalan: C481
                'value' => rand(0, 60)                // Masalan: Tezlik yoki ko'rsatkich
            ];
        }
        return $items;
    }
    public function getUsers()
    {
        // Ism va familiyani birlashtirib 'name' sifatida qaytarish
        $users = User::selectRaw("id, first_name + ' ' + last_name as name")->get();
        return response()->json($users);
    }
}