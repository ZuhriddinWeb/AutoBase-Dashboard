<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Keshni tozalash (Spatie uchun muhim)
        // app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // // 2. Resurslar ro'yxati
        // $resources = [
        //     'users',            // Foydalanuvchilar
        //     'roles',            // Rollar
        //     'pages',            // Sahifalar
        //     'groups',           // Guruhlar
        //     'transport_groups', // Transport Guruhlari
        //     'geozone_groups',   // Geozona Guruhlari
        //     'machines',         // Texnikalar
        //     'wialon_settings',  // Wialon API
        //     'logs'              // Tizim loglari
        // ];

        // $actions = ['view', 'create', 'edit', 'delete'];

        // // 3. Ruxsatlarni yaratish
        // foreach ($resources as $res) {
        //     foreach ($actions as $act) {
        //         Permission::firstOrCreate(['name' => "{$res}_{$act}"]);
        //     }
        // }

        // 4. "Super Admin" rolini yaratish va unga HAMMA ruxsatni berish
        $role = Role::firstOrCreate(['name' => 'Super Admin']);
        $role->givePermissionTo(Permission::all());

        // 5. User yaratish (Login: admin, Parol: password)
        // Eslatma: Sizning User modelingizda 'first_name' va 'last_name' bo'lishi mumkin
        $user = User::firstOrCreate(
            ['login' => 'MZN'], // Qidirish sharti
            [
                'first_name' => 'Super',
                'last_name'  => 'admin',
                'password'   => Hash::make('zzzz2222**'),
                'status'     => 'active'
            ]
        );

        // 6. Rolni biriktirish
        $user->assignRole($role);

        $this->command->info('Super Admin va Ruxsatlar muvaffaqiyatli yaratildi!');
    }
}