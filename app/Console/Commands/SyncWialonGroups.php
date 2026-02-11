<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WialonSetting;
use App\Models\TransportGroup;
use App\Models\GeozoneGroup;
use Illuminate\Support\Facades\Http;

class SyncWialonGroups extends Command
{
    protected $signature = 'wialon:sync-groups';
    protected $description = 'Wialondan Transport va Geozona guruhlarini yuklash';

    public function handle()
    {
        $this->info('--- Guruhlarni yuklash boshlandi ---');

        // 1. Sozlamalar va Login (Avvalgi kod bilan bir xil)
        $setting = WialonSetting::where('status', 'active')->first();
        if (!$setting) {
            $this->error('Sozlamalar yo\'q!');
            return;
        }

        $baseUrl = rtrim($setting->base_url, '/');
        
        // Login
        $loginRes = Http::get($baseUrl . '/wialon/ajax.html', [
            'svc' => 'token/login',
            'params' => json_encode(['token' => $setting->token])
        ]);
        $loginData = $loginRes->json();
        
        if (!isset($loginData['eid'])) {
            $this->error('Login xatosi!');
            return;
        }
        $sid = $loginData['eid'];

        // ==========================================
        // 2. TRANSPORT GURUHLARINI OLISH (avl_unit_group)
        // ==========================================
        $this->info('Transport guruhlari qidirilmoqda...');
        
        $this->fetchAndSave($baseUrl, $sid, 'avl_unit_group', TransportGroup::class);

        // ==========================================
        // 3. GEOZONA GURUHLARINI OLISH (avl_resource)
        // ==========================================
        $this->info('Geozona guruhlari (Resurslar) qidirilmoqda...');

        $this->fetchAndSave($baseUrl, $sid, 'avl_resource', GeozoneGroup::class);

        // Logout
        Http::get($baseUrl . '/wialon/ajax.html', ['svc' => 'core/logout', 'sid' => $sid]);
        
        $this->info('--- Barchasi muvaffaqiyatli yakunlandi ---');
    }

    // Kodni qisqartirish uchun yordamchi funksiya
    private function fetchAndSave($baseUrl, $sid, $wialonType, $modelClass)
    {
        $params = [
            'spec' => [
                'itemsType' => $wialonType, // avl_unit_group yoki avl_resource
                'propName' => 'sys_name',
                'propValueMask' => '*',
                'sortType' => 'sys_name'
            ],
            'force' => 1,
            'flags' => 1, // Bizga faqat ID va Name kerak
            'from' => 0,
            'to' => 0
        ];

        $response = Http::get($baseUrl . '/wialon/ajax.html', [
            'svc' => 'core/search_items',
            'sid' => $sid,
            'params' => json_encode($params)
        ]);

        $data = $response->json();

        if (isset($data['items'])) {
            $count = 0;
            foreach ($data['items'] as $item) {
                $modelClass::updateOrCreate(
                    ['wialon_id' => $item['id']],
                    ['name' => $item['nm']]
                );
                $count++;
            }
            $this->info("   -> $count ta guruh saqlandi ($wialonType).");
        } else {
            $this->error("   -> $wialonType topilmadi yoki xatolik.");
        }
    }
}