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
            ['name' => 'super-admin', 'label' => 'Super Admin'],
            ['name' => 'opd', 'label' => 'Organisasi Perangkat Daerah'],
            ['name' => 'penanggung-jawab', 'label' => 'Penanggung Jawab Data'],
            ['name' => 'riviewer', 'label' => 'Riviewer Data'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }

         User::create([
            'name' => 'Admin',
            'email' => 'retmujago@gmail.com',
            'role_id' =>1,
            'password' => Hash::make('admin123'), // ganti sesuai kebutuhan
            'email_verified_at' => Carbon::now(), // langsung dianggap sudah verifikasi
        ]);
    }
}
