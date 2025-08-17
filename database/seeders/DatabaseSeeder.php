<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void

    {
        $roles = [
            ['name' => 'admin', 'label' => 'Administrator'],
            ['name' => 'opd', 'label' => 'Organisasi Perangkat Daerah'],
            ['name' => 'penanggung-jawab', 'label' => 'Penanggung Jawab Data'],
            ['name' => 'pengelola', 'label' => 'Pengelola Data'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
         User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'role_id' =>1,
            'password' => Hash::make('admin123'), // ganti sesuai kebutuhan
        ]);
    }
}
