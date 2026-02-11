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
    private function getWialonErrorMessage($code) {
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
}