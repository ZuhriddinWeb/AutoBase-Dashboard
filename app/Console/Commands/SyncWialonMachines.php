<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WialonSetting;
use App\Models\Machine;
use Illuminate\Support\Facades\Http;

class SyncWialonMachines extends Command
{
    // Terminalda ishlatiladigan buyruq nomi
    protected $signature = 'wialon:sync';
    protected $description = 'Wialondan texnikalarni tortib olish';

    public function handle()
    {
        $this->info('--- Sinxronizatsiya boshlandi ---');

        // 1. Sozlamalarni olish
        $setting = WialonSetting::where('status', 'active')->first();
        if (!$setting) {
            $this->error('Xatolik: Admin panelda Wialon sozlamalari topilmadi!');
            return;
        }

        $baseUrl = rtrim($setting->base_url, '/');
        
        // 2. Login qilish (Sessiya ID olish)
        // Sizda hozirgina ishlagan mantiq shu:
        $loginResponse = Http::get($baseUrl . '/wialon/ajax.html', [
            'svc' => 'token/login',
            'params' => json_encode(['token' => $setting->token])
        ]);

        $loginData = $loginResponse->json();

        if (isset($loginData['error'])) {
            $this->error('Login Xatosi Kod: ' . $loginData['error']);
            return;
        }

        $sid = $loginData['eid']; // Sessiya ID (masalan: b4474...)
        $this->info("Login muvaffaqiyatli. Sessiya ID olindi.");

        // 3. Texnikalar ro'yxatini olish (core/search_items)
        // flags: 1025 (1 = Base, 1024 = Unique ID/IMEI)
        $searchParams = [
            'spec' => [
                'itemsType' => 'avl_unit',
                'propName' => 'sys_name',
                'propValueMask' => '*',
                'sortType' => 'sys_name'
            ],
            'force' => 1,
            'flags' => 1025, 
            'from' => 0,
            'to' => 0
        ];

        $dataResponse = Http::get($baseUrl . '/wialon/ajax.html', [
            'svc' => 'core/search_items',
            'sid' => $sid,
            'params' => json_encode($searchParams)
        ]);

        $data = $dataResponse->json();

        if (isset($data['items'])) {
            $count = 0;
            foreach ($data['items'] as $item) {
                // Bazaga yozish yoki yangilash
                Machine::updateOrCreate(
                    ['wialon_id' => $item['id']], // Qidirish sharti
                    [
                        'name' => $item['nm'], // Yangi nomi
                        'imei' => $item['uid'] ?? null // IMEI
                    ]
                );
                $count++;
            }
            $this->info("Natija: $count ta texnika yangilandi!");
        } else {
            $this->error("Ro'yxat bo'sh yoki xatolik yuz berdi.");
        }

        // 4. Sessiyani yopish (Logout)
        Http::get($baseUrl . '/wialon/ajax.html', [
            'svc' => 'core/logout',
            'sid' => $sid
        ]);
        
        $this->info('--- Jarayon tugadi ---');
    }
}