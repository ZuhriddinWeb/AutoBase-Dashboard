<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WialonSetting; // Modelni ulash
use Illuminate\Support\Facades\Http; // So'rov yuborish uchun
use Illuminate\Support\Facades\Artisan;

class WialonController extends Controller
{
    // Ulanishni tekshirish funksiyasi
    public function checkConnection()
    {
        // 1. Bazadan 'active' holatdagi sozlamani olamiz (siz admin panelda kiritgan ma'lumot)
        $setting = WialonSetting::where('status', 'active')->first();

        if (!$setting) {
            return response()->json(['message' => 'Sozlamalar topilmadi'], 404);
        }

        // 2. URLni to'g'rilash (oxirida / belgisini olib tashlash)
        $baseUrl = rtrim($setting->base_url, '/');
        $fullUrl = $baseUrl . '/wialon/ajax.html';

        // 3. Wialon API ga so'rov yuborish (Login qilish)
        try {
            $response = Http::get($fullUrl, [
                'svc' => 'token/login',
                'params' => json_encode(['token' => $setting->token])
            ]);

            $data = $response->json();

            // 4. Natijani qaytarish
            if (isset($data['error'])) {
                // Agar NGMK serveri xato qaytarsa (masalan, Error 7)
                return response()->json([
                    'status' => 'error',
                    'error_code' => $data['error'],
                    'message' => $this->getWialonErrorMessage($data['error']),
                    'raw' => $data
                ], 400);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Ulanish muvaffaqiyatli!',
                'session_id' => $data['eid'], // Bu ID keyingi so'rovlar uchun kerak
                'user' => $data['au'],
                'server_time' => date('Y-m-d H:i:s', $data['tm'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Serverga ulanib bo\'lmadi. VPN yoki Internetni tekshiring.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    public function store(Request $request)
    {
        // 1. Validatsiya (Resource ID majburiy bo'lsa required qiling)
        $validated = $request->validate([
            'base_url' => 'required|string',
            'token' => 'required|string',
            'username' => 'nullable|string',
            'password' => 'nullable|string',
            'resource_id' => 'nullable|numeric', // Agar majburiy bo'lsa 'required' deng
        ]);

        // 2. Status va Resource ID ni tekshirish
        $data = $request->all();

        // Statusni majburiy "active" qilamiz
        $data['status'] = 'active';

        // Resource ID bo'sh kelsa, 0 yoki null berib yuboramiz (xato bermasligi uchun)
        if (empty($data['resource_id'])) {
            $data['resource_id'] = 0; // Yoki null, agar bazada nullable bo'lsa
        }

        // 3. Bazaga yozish
        $setting = WialonSetting::create($data);

        return response()->json([
            'message' => 'Wialon sozlamalari saqlandi',
            'data' => $setting
        ]);
    }
    // Xatolik kodlarini tushunarli qilish uchun yordamchi funksiya
    private function getWialonErrorMessage($code)
    {
        $errors = [
            4 => 'Noto\'g\'ri kiritilgan ma\'lumotlar (Token xato)',
            7 => 'IP manzil ruxsat etilmagan (ACCESS_DENIED_BY_HOST_MASK). VPN kerak.',
            8 => 'Login yoki parol xato',
            1 => 'Sessiya xatosi'
        ];
        return $errors[$code] ?? 'Noma\'lum xatolik';
    }
    public function syncMachines()
    {
        try {
            // Texnikalarni tortib olish buyrug'ini ishga tushiramiz
            Artisan::call('wialon:sync');

            // Logini olish (ixtiyoriy, natijani ko'rish uchun)
            $output = Artisan::output();

            return response()->json([
                'message' => 'Texnikalar sinxronizatsiyasi boshlandi!',
                'details' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Xatolik yuz berdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function syncGroups()
    {
        try {
            // Guruhlarni tortib olish buyrug'ini ishga tushiramiz
            Artisan::call('wialon:sync-groups');

            return response()->json([
                'message' => 'Guruhlar sinxronizatsiyasi boshlandi!',
                'details' => Artisan::output()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Xatolik yuz berdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    // Barcha ma'lumotlarni qanday formatda kelishini ko'rish uchun test funksiya
    public function exploreData()
    {
        // 1. Sozlamalarni olish
        $setting = WialonSetting::where('status', 'active')->first();
        if (!$setting)
            return response()->json(['message' => 'Sozlamalar topilmadi'], 404);

        $baseUrl = rtrim($setting->base_url, '/');
        $fullUrl = $baseUrl . '/wialon/ajax.html';

        // 2. Tizimga kirish va Session ID (eid) olish
        $login = Http::get($fullUrl, [
            'svc' => 'token/login',
            'params' => json_encode(['token' => $setting->token])
        ])->json();

        if (isset($login['error'])) {
            return response()->json(['error' => 'Login xato: ' . $login['error']]);
        }

        $sid = $login['eid'];

        // 3. Wialon dan 5 ta mashinaning BARCHA ma'lumotlarini so'raymiz
        $searchParams = [
            "spec" => [
                "itemsType" => "avl_unit", // Faqat texnikalar
                "propName" => "sys_name",
                "propValueMask" => "*",
                "sortType" => "sys_name"
            ],
            "force" => 1,
            "flags" => 4611686018427387903, // 4611686018427387903 - Bu Wialon'da "Barcha ma'lumotlarni ber" degan kod (Flag)
            "from" => 0,
            "to" => 5 // Ko'p qotmasligi uchun faqat 5 ta mashinani ko'ramiz
        ];

        $data = Http::get($fullUrl, [
            'svc' => 'core/search_items',
            'sid' => $sid,
            'params' => json_encode($searchParams)
        ])->json();

        // Natijani ekranga chiqaramiz
        return response()->json($data);
    }
public function getDashboardData()
    {
        try {
            $setting = WialonSetting::where('status', 'active')->first();
            if (!$setting)
                return response()->json(['error' => 'Wialon sozlamalari topilmadi'], 404);

            $baseUrl = rtrim($setting->base_url, '/');
            $fullUrl = $baseUrl . '/wialon/ajax.html';

            $loginResponse = Http::get($fullUrl, [
                'svc' => 'token/login',
                'params' => json_encode(['token' => $setting->token])
            ])->json();

            if (isset($loginResponse['error']))
                return response()->json(['error' => 'Wialon login xatosi', 'code' => $loginResponse['error']], 401);

            $sid = $loginResponse['eid'];

            // 1. MASHINALARNI OLISH
            $unitsResponse = Http::get($fullUrl, [
                'svc' => 'core/search_items',
                'sid' => $sid,
                'params' => json_encode([
                    "spec" => ["itemsType" => "avl_unit", "propName" => "sys_name", "propValueMask" => "*", "sortType" => "sys_name"],
                    "force" => 1,
                    "flags" => 4611686018427387903,
                    "from" => 0,
                    "to" => 300 // Ko'proq mashina olish uchun
                ])
            ])->json();

            // 2. GEOZONALARNI OLISH
            $resourcesResponse = Http::get($fullUrl, [
                'svc' => 'core/search_items',
                'sid' => $sid,
                'params' => json_encode([
                    "spec" => ["itemsType" => "avl_resource", "propName" => "sys_name", "propValueMask" => "*", "sortType" => "sys_name"],
                    "force" => 1,
                    "flags" => 4611686018427387903,
                    "from" => 0,
                    "to" => 10
                ])
            ])->json();

            $items = $unitsResponse['items'] ?? [];
            $resources = $resourcesResponse['items'] ?? [];

            // ---------------------------------------------------------
            // 3. FAQAT "ZZZ_" GEOZONALARNI AJRATIB OLISH VA TAYYORLASH
            // ---------------------------------------------------------
            $allGeozones = [];
            $groupedZones = []; 

            foreach ($resources as $res) {
                if (isset($res['zl']) && is_array($res['zl'])) {
                    foreach ($res['zl'] as $zone) {
                        $rawName = $zone['n'] ?? '';
                        
                        // Faqat ZZZ_ bilan boshlanganlarni olamiz
                        if (str_starts_with($rawName, 'ZZZ_') && isset($zone['b'])) {
                            // "ZZZ_" so'zini kesib tashlaymiz
                            $cleanName = str_replace('ZZZ_', '', $rawName);
                            
                            // JSON kaliti uchun bo'sh joylarni probelga almashtiramiz
                            $safeGeoName = str_replace([' ', '.', ','], '_', $cleanName);
                            $zoneKey = 'Zone_' . $safeGeoName;

                            $allGeozones[] = [
                                'name' => $cleanName,
                                'key'  => $zoneKey, // Mashinani shu kalitga tiqamiz
                                'min_x' => $zone['b']['min_x'],
                                'max_x' => $zone['b']['max_x'],
                                'min_y' => $zone['b']['min_y'],
                                'max_y' => $zone['b']['max_y'],
                            ];

                            // E'TIBOR BERING: Vidjetda ko'rinishi uchun bo'sh bo'lsa ham massiv ochib qo'yamiz!
                            $groupedZones[$zoneKey] = [];
                        }
                    }
                }
            }

            // HISOBLASH O'ZGARUVCHILARI
            $totalMachines = count($items);
            $activeDrivers = 0; $totalFuel = 0; $totalDistance = 0;
            $greenGrid = []; $blueGrid = []; $topDrivers = [];
            $geozoneList = []; // Umumiy ro'yxat
            $maxSpeed = 0; $maxRpm = 0;

            foreach ($items as $item) {
                $name = $item['nm'] ?? 'Noma\'lum';
                $speed = 0; $posX = 0; $posY = 0;

                if (isset($item['pos'])) {
                    $speed = (float) ($item['pos']['s'] ?? 0);
                    $posX = (float) ($item['pos']['x'] ?? 0);
                    $posY = (float) ($item['pos']['y'] ?? 0);
                } elseif (isset($item['prms']['speed'])) {
                    $speed = (float) $item['prms']['speed']['v'];
                }

                $prms = $item['prms'] ?? [];
                $fuel = isset($prms['clls1']) ? (float) $prms['clls1']['v'] : 0;
                $rpm = isset($prms['timp']) ? (float) $prms['timp']['v'] : 0;
                $mileage = isset($prms['mileage']) ? (float) $prms['mileage']['v'] : 0;

                $totalFuel += $fuel; $totalDistance += $mileage;
                if ($speed > $maxSpeed) $maxSpeed = $speed;
                if ($rpm > $maxRpm) $maxRpm = $rpm;

                if ($speed > 0 || $rpm > 0) {
                    $activeDrivers++;
                    $greenGrid[] = ['label' => mb_substr($name, 0, 10), 'value' => $speed . ' km/h'];
                } else {
                    $blueGrid[] = ['label' => mb_substr($name, 0, 10), 'value' => '0 km/h'];
                }

                $topDrivers[] = ['name' => mb_substr($name, 0, 15), 'status' => $fuel > 0 ? round($fuel) . ' L' : '0 L'];

                // ------------------------------------------------------------------
                // 4. MASHINA QAYSI GEOZONADA EKKANINI HISBOBLASH
                // ------------------------------------------------------------------
                $geoName = 'Tashqarida';
                $currentZoneKey = null;

                if ($posX > 0 && $posY > 0) {
                    foreach ($allGeozones as $zone) {
                        if ($posX >= $zone['min_x'] && $posX <= $zone['max_x'] &&
                            $posY >= $zone['min_y'] && $posY <= $zone['max_y']) {
                            $geoName = $zone['name'];
                            $currentZoneKey = $zone['key'];
                            break;
                        }
                    }
                }

                $statusText = $speed > 0 ? "📍 {$geoName} ({$speed} km/h)" : "📍 {$geoName}";
                $geozoneList[] = ['name' => mb_substr($name, 0, 15), 'status' => $statusText];

                // Agar mashina biror zonada bo'lsa, uni o'sha zonaning maxsus ro'yxatiga tiqamiz
                if ($currentZoneKey) {
                    $groupedZones[$currentZoneKey][] = [
                        'name' => mb_substr($name, 0, 15),
                        'status' => $speed > 0 ? "Harakatda ({$speed} km/h)" : "To'xtab turibdi"
                    ];
                }
            }

            // MA'LUMOTLARNI JAMLASH
            $responseData = [
                'TotalMachines' => $totalMachines,
                'ActiveDrivers' => $activeDrivers,
                'FuelConsumption' => round($totalFuel) . ' L',
                'TotalDistance' => round($totalDistance) . ' km',
                'AlertsCount' => rand(0, 5),
                'OnlineUsers' => rand(1, 4),
                'SpeedGauge' => $maxSpeed,
                'RpmGauge' => round($maxRpm / 1000, 1),
                'GreenGrid' => array_slice($greenGrid, 0, 100),
                'BlueGrid' => array_slice($blueGrid, 0, 100),
                'LastEvents' => array_slice($topDrivers, 0, 100),
                'TopDrivers' => array_slice($topDrivers, 0, 100),
                'GeozoneList' => array_slice($geozoneList, 0, 100),
                'ServerStatus' => rand(40, 60),
                'DbStatus' => rand(30, 50),
                'WialonStatus' => 100
            ];

            // Barcha alohida topilgan geozonalarni (Zone_...) javobga qo'shamiz
            foreach ($groupedZones as $key => $vehicles) {
                $responseData[$key] = array_slice($vehicles, 0, 100);
            }

            return response()->json($responseData);

        } catch (\Exception $e) {
            return response()->json(['error' => 'API da xatolik', 'message' => $e->getMessage()], 500);
        }
    }
    public function testWialonData()
    {
        $setting = WialonSetting::where('status', 'active')->first();
        if (!$setting) {
            return response()->json(['error' => 'Wialon token topilmadi']);
        }

        $baseUrl = rtrim($setting->base_url, '/');
        $fullUrl = $baseUrl . '/wialon/ajax.html';

        // 1. Tizimga kirish (Login)
        $login = Http::get($fullUrl, [
            'svc' => 'token/login',
            'params' => json_encode(['token' => $setting->token])
        ])->json();

        if (isset($login['error'])) {
            return response()->json(['error' => 'Login xatosi', 'details' => $login]);
        }
        $sid = $login['eid'];

        // 2. MASHINALARNI (Units) barcha parametrlari bilan olish (2 ta mashina yetarli)
        $units = Http::get($fullUrl, [
            'svc' => 'core/search_items',
            'sid' => $sid,
            'params' => json_encode([
                "spec" => [
                    "itemsType" => "avl_unit",
                    "propName" => "sys_name",
                    "propValueMask" => "*",
                    "sortType" => "sys_name"
                ],
                "force" => 1,
                "flags" => 4611686018427387903, // Barcha ma'lumotlar huquqi
                "from" => 0,
                "to" => 2
            ])
        ])->json();

        // 3. GEOZONALAR VA RESURSLARNI (avl_resource) olish
        // Wialonda geozonalar mashinalarning ichida emas, alohida "Resurs" sifatida saqlanadi
        $resources = Http::get($fullUrl, [
            'svc' => 'core/search_items',
            'sid' => $sid,
            'params' => json_encode([
                "spec" => [
                    "itemsType" => "avl_resource",
                    "propName" => "sys_name",
                    "propValueMask" => "*",
                    "sortType" => "sys_name"
                ],
                "force" => 1,
                "flags" => 4611686018427387903,
                "from" => 0,
                "to" => 2
            ])
        ])->json();

        // 4. Ekranga barchasini toza (Raw) JSON qilib chiqarish
        return response()->json([
            'MESSAGE' => 'WIALON API DAN KELGAN XOM (RAW) MA\'LUMOTLAR',
            '1_VEHICLES_DATA' => $units,
            '2_RESOURCES_AND_GEOZONES' => $resources
        ]);
    }
public function syncGeozones()
    {
        $setting = WialonSetting::where('status', 'active')->first();
        if (!$setting) return response()->json(['message' => 'Sozlamalar topilmadi'], 404);

        $baseUrl = rtrim($setting->base_url, '/');
        $fullUrl = $baseUrl . '/wialon/ajax.html';

        try {
            // 1. Login
            $login = Http::get($fullUrl, [
                'svc' => 'token/login',
                'params' => json_encode(['token' => $setting->token])
            ])->json();

            if (isset($login['error'])) return response()->json(['message' => 'Login xatosi'], 401);
            $sid = $login['eid'];

            // 2. Resurslarni (va ichidagi geozonalarni) olish
            // itemsType = avl_resource bo'lishi SHART
            $response = Http::get($fullUrl, [
                'svc' => 'core/search_items',
                'sid' => $sid,
                'params' => json_encode([
                    "spec" => [
                        "itemsType" => "avl_resource",
                        "propName" => "sys_name",
                        "propValueMask" => "*",
                        "sortType" => "sys_name"
                    ],
                    "force" => 1,
                    "flags" => 4097, // Geozonalar (4096) + Base (1)
                    "from" => 0,
                    "to" => 10
                ])
            ])->json();

            $resources = $response['items'] ?? [];
            $count = 0;
            $savedNames = [];

            // 3. Geozonalarni bazaga saqlash
            foreach ($resources as $resource) {
                if (isset($resource['zl']) && is_array($resource['zl'])) {
                    foreach ($resource['zl'] as $zoneId => $zoneData) {
                        
                        // Bu yerda Geozone modeliga saqlaymiz
                        // Misol uchun: App\Models\Geozone::updateOrCreate(...)
                        
                        // Model bo'lmasa, shunchaki ro'yxatga olib turamiz:
                        $name = $zoneData['n'];
                        $description = $zoneData['d'] ?? '';
                        
                        // Eslatma: Bazaga yozish kodi (Model bo'lsa ochib qo'ying):
                        /*
                        \App\Models\Geozone::updateOrCreate(
                            ['wialon_id' => $zoneData['id'], 'resource_id' => $resource['id']],
                            [
                                'name' => $name,
                                'description' => $description,
                                'type' => $zoneData['t'], // 1-line, 2-polygon, 3-circle
                                'color' => $zoneData['c'] ?? 0,
                                'area' => $zoneData['ar'] ?? 0,
                                'perimeter' => $zoneData['pr'] ?? 0
                            ]
                        );
                        */
                        
                        $savedNames[] = $name;
                        $count++;
                    }
                }
            }

            return response()->json([
                'message' => "Sinxronizatsiya yakunlandi!",
                'details' => "Jami {$count} ta geozona topildi. \n" . implode(", ", array_slice($savedNames, 0, 10)) . (count($savedNames) > 10 ? "..." : "")
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Xatolik', 'error' => $e->getMessage()], 500);
        }
    }
}