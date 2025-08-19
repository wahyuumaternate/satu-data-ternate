<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void

    {
        $roles = [
            ['name' => 'super-admin', 'guard_name' => 'web'],
            ['name' => 'opd', 'guard_name' => 'web'],
            ['name' => 'penanggung-jawab', 'guard_name' => 'web'],
            ['name' => 'reviewer', 'guard_name' => 'web'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }

       

         User::create([
            'name' => 'Admin',
            'email' => 'retmujago@gmail.com',
            'password' => Hash::make('admin123'), // ganti sesuai kebutuhan
            'email_verified_at' => Carbon::now(), // langsung dianggap sudah verifikasi
        ]);
         // Assign role ke user pertama sebagai super-admin (optional)
        $superAdmin = User::first();
        if ($superAdmin) {
            $superAdmin->assignRole('super-admin');
        }
    }
}
