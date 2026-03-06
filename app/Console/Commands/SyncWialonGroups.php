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
    protected $description = 'Wialondan Transport guruhlari va GEOZONALARNI yuklash';

    public function handle()
    {
        $this->info('--- Yuklash boshlandi ---');

        $setting = WialonSetting::where('status', 'active')->first();
        if (!$setting) {
            $this->error('Sozlamalar yo\'q!');
            return;
        }

        $baseUrl = rtrim($setting->base_url, '/');

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
        // 1. TRANSPORT GURUHLARINI OLISH
        // ==========================================
        $this->info('Transport guruhlari qidirilmoqda...');
        $this->syncTransportGroups($baseUrl, $sid);

        // ==========================================
        // 2. GEOZONALARNI OLISH (avl_resource va zl)
        // ==========================================
        $this->info('Geozonalar qidirilmoqda...');
        $this->syncGeozones($baseUrl, $sid);

        Http::get($baseUrl . '/wialon/ajax.html', ['svc' => 'core/logout', 'sid' => $sid]);

        $this->info('--- Barchasi muvaffaqiyatli yakunlandi ---');
    }

    private function syncTransportGroups($baseUrl, $sid)
    {
        $params = [
            'spec' => [
                'itemsType' => 'avl_unit_group',
                'propName' => 'sys_name',
                'propValueMask' => '*',
                'sortType' => 'sys_name'
            ],
            'force' => 1,
            'flags' => 1, // Faqat asosiy ma'lumotlar
            'from' => 0,
            'to' => 0
        ];

        $response = Http::get($baseUrl . '/wialon/ajax.html', [
            'svc' => 'core/search_items',
            'sid' => $sid,
            'params' => json_encode($params)
        ])->json();

        if (isset($response['items'])) {
            $count = 0;
            foreach ($response['items'] as $item) {
                TransportGroup::updateOrCreate(
                    ['wialon_id' => $item['id']],
                    ['name' => $item['nm']]
                );
                $count++;
            }
            $this->info("   -> $count ta transport guruhi saqlandi.");
        } else {
            $this->error("   -> Transport guruhlari topilmadi.");
        }
    }

    private function syncGeozones($baseUrl, $sid)
    {
        $params = [
            'spec' => [
                'itemsType' => 'avl_resource',
                'propName' => 'sys_name',
                'propValueMask' => '*',
                'sortType' => 'sys_name'
            ],
            'force' => 1,
            'flags' => 4097, // 4096 (Geozonalar - zl) + 1 (Base info)
            'from' => 0,
            'to' => 0
        ];

        $response = Http::get($baseUrl . '/wialon/ajax.html', [
            'svc' => 'core/search_items',
            'sid' => $sid,
            'params' => json_encode($params)
        ])->json();

        if (isset($response['items'])) {
            $count = 0;
            foreach ($response['items'] as $resource) {
                $resourceId = $resource['id'];

                if (isset($resource['zl']) && is_array($resource['zl'])) {
                    foreach ($resource['zl'] as $zoneId => $zoneData) {

                        $zoneName = $zoneData['n'] ?? '';

                        // FAKAT "ZZZ_" BILAN BOSHLANGANLARNI OLAMIZ
                        if (str_starts_with($zoneName, 'ZZZ_')) {
                            try {
                                $bounds = $zoneData['b'] ?? null;

                                // ZZZ_ yozuvini olib tashlab toza nomini saqlaymiz (xohlasangiz olib tashlamasligingiz ham mumkin)
                                $cleanName = str_replace('ZZZ_', '', $zoneName);

                                GeozoneGroup::updateOrCreate(
                                    [
                                        'wialon_id' => $zoneData['id'],
                                        'resource_id' => $resourceId
                                    ],
                                    [
                                        'name' => $cleanName, // Toza nom bilan saqlaydi
                                        'description' => $zoneData['d'] ?? null,
                                        'type' => $zoneData['t'] ?? null,
                                        'min_x' => $bounds['min_x'] ?? null,
                                        'min_y' => $bounds['min_y'] ?? null,
                                        'max_x' => $bounds['max_x'] ?? null,
                                        'max_y' => $bounds['max_y'] ?? null,
                                        'cen_x' => $bounds['cen_x'] ?? null,
                                        'cen_y' => $bounds['cen_y'] ?? null,
                                    ]
                                );
                                $count++;
                            } catch (\Exception $e) {
                                $this->error("Geozona saqlanmadi (ID: {$zoneData['id']}): " . $e->getMessage());
                            }
                        }
                    }
                }
            }
            $this->info("   -> Jami $count ta 'ZZZ_' bilan boshlangan Geozona saqlandi.");
        } else {
            $this->error("   -> Geozonalar topilmadi.");
        }
    }
}