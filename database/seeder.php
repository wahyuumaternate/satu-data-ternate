<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OpenDataTernateSeeder extends Seeder
{
    /**
     * Run the database seeds untuk Open Data Ternate
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CategoriesSeeder::class,
            OrganizationsSeeder::class,
            TagsSeeder::class,
            UsersSeeder::class,
            SystemSettingsSeeder::class,
        ]);
    }
}

/**
 * Seeder untuk Categories
 */
class CategoriesSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'id' => Str::uuid(),
                'name' => 'Pemerintahan dan Demokrasi',
                'slug' => 'pemerintahan-demokrasi',
                'description' => 'Data terkait struktur pemerintahan, pemilihan, dan proses demokratis',
                'icon' => 'government',
                'color' => '#3B82F6',
                'sort_order' => 1,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Kesehatan',
                'slug' => 'kesehatan',
                'description' => 'Data kesehatan masyarakat, fasilitas kesehatan, dan program kesehatan',
                'icon' => 'health',
                'color' => '#EF4444',
                'sort_order' => 2,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Pendidikan',
                'slug' => 'pendidikan',
                'description' => 'Data sektor pendidikan, sekolah, siswa, dan tenaga pendidik',
                'icon' => 'education',
                'color' => '#10B981',
                'sort_order' => 3,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Ekonomi dan Keuangan',
                'slug' => 'ekonomi-keuangan',
                'description' => 'Data ekonomi daerah, APBD, pajak, dan retribusi',
                'icon' => 'economy',
                'color' => '#F59E0B',
                'sort_order' => 4,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Transportasi dan Mobilitas',
                'slug' => 'transportasi-mobilitas',
                'description' => 'Data transportasi publik, lalu lintas, dan infrastruktur jalan',
                'icon' => 'transport',
                'color' => '#8B5CF6',
                'sort_order' => 5,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Lingkungan Hidup',
                'slug' => 'lingkungan-hidup',
                'description' => 'Data kualitas lingkungan, sampah, dan konservasi alam',
                'icon' => 'environment',
                'color' => '#059669',
                'sort_order' => 6,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Kependudukan',
                'slug' => 'kependudukan',
                'description' => 'Data demografi, penduduk, dan administrasi kependudukan',
                'icon' => 'population',
                'color' => '#DC2626',
                'sort_order' => 7,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Pariwisata dan Kebudayaan',
                'slug' => 'pariwisata-kebudayaan',
                'description' => 'Data objek wisata, budaya, dan industri kreatif',
                'icon' => 'tourism',
                'color' => '#F97316',
                'sort_order' => 8,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Sosial dan Kesejahteraan',
                'slug' => 'sosial-kesejahteraan',
                'description' => 'Data program sosial, bantuan sosial, dan kesejahteraan masyarakat',
                'icon' => 'social',
                'color' => '#EC4899',
                'sort_order' => 9,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Infrastruktur dan Pembangunan',
                'slug' => 'infrastruktur-pembangunan',
                'description' => 'Data pembangunan infrastruktur, pekerjaan umum, dan tata ruang',
                'icon' => 'infrastructure',
                'color' => '#6B7280',
                'sort_order' => 10,
            ],
        ];

        foreach ($categories as $category) {
            $category['created_at'] = Carbon::now();
            $category['updated_at'] = Carbon::now();
            DB::table('categories')->insert($category);
        }
    }
}

/**
 * Seeder untuk Organizations (OPD Kota Ternate)
 */
class OrganizationsSeeder extends Seeder
{
    public function run()
    {
        $organizations = [
            [
                'id' => Str::uuid(),
                'name' => 'Sekretariat Daerah Kota Ternate',
                'slug' => 'sekda-ternate',
                'acronym' => 'SEKDA',
                'description' => 'Sekretariat Daerah Kota Ternate sebagai unsur staf yang membantu Walikota',
                'type' => 'sekretariat',
                'address' => 'Jl. Pahlawan Revolusi, Ternate',
                'phone' => '0921-3121234',
                'email' => 'sekda@ternatekota.go.id',
                'website' => 'https://ternatekota.go.id',
                'head_name' => 'Dr. H. Ahmad Suharto, S.Sos, M.Si',
                'head_title' => 'Sekretaris Daerah',
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Dinas Komunikasi dan Informatika',
                'slug' => 'diskominfo-ternate',
                'acronym' => 'DISKOMINFO',
                'description' => 'Dinas yang menyelenggarakan urusan komunikasi, informatika, statistik dan persandian',
                'type' => 'dinas',
                'address' => 'Jl. Sultan Khairun, Ternate',
                'phone' => '0921-3125678',
                'email' => 'diskominfo@ternatekota.go.id',
                'website' => 'https://diskominfo.ternatekota.go.id',
                'head_name' => 'Ir. Budi Santoso, M.Kom',
                'head_title' => 'Kepala Dinas',
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Dinas Kesehatan',
                'slug' => 'dinkes-ternate',
                'acronym' => 'DINKES',
                'description' => 'Dinas yang menyelenggarakan urusan kesehatan masyarakat',
                'type' => 'dinas',
                'address' => 'Jl. Cempaka, Ternate',
                'phone' => '0921-3126789',
                'email' => 'dinkes@ternatekota.go.id',
                'website' => 'https://dinkes.ternatekota.go.id',
                'head_name' => 'dr. Siti Aminah, M.Kes',
                'head_title' => 'Kepala Dinas',
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Dinas Pendidikan',
                'slug' => 'disdik-ternate',
                'acronym' => 'DISDIK',
                'description' => 'Dinas yang menyelenggarakan urusan pendidikan',
                'type' => 'dinas',
                'address' => 'Jl. Yos Sudarso, Ternate',
                'phone' => '0921-3127890',
                'email' => 'disdik@ternatekota.go.id',
                'website' => 'https://disdik.ternatekota.go.id',
                'head_name' => 'Drs. Muhammad Ali, M.Pd',
                'head_title' => 'Kepala Dinas',
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Dinas Pekerjaan Umum dan Penataan Ruang',
                'slug' => 'dpupr-ternate',
                'acronym' => 'DPUPR',
                'description' => 'Dinas yang menyelenggarakan urusan pekerjaan umum dan penataan ruang',
                'type' => 'dinas',
                'address' => 'Jl. Ahmad Yani, Ternate',
                'phone' => '0921-3128901',
                'email' => 'dpupr@ternatekota.go.id',
                'website' => 'https://dpupr.ternatekota.go.id',
                'head_name' => 'Ir. Rahmat Hidayat, M.T',
                'head_title' => 'Kepala Dinas',
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Dinas Perhubungan',
                'slug' => 'dishub-ternate',
                'acronym' => 'DISHUB',
                'description' => 'Dinas yang menyelenggarakan urusan perhubungan',
                'type' => 'dinas',
                'address' => 'Jl. Raya Bandara, Ternate',
                'phone' => '0921-3129012',
                'email' => 'dishub@ternatekota.go.id',
                'website' => 'https://dishub.ternatekota.go.id',
                'head_name' => 'Drs. Andi Wijaya, M.T',
                'head_title' => 'Kepala Dinas',
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Dinas Lingkungan Hidup',
                'slug' => 'dlh-ternate',
                'acronym' => 'DLH',
                'description' => 'Dinas yang menyelenggarakan urusan lingkungan hidup',
                'type' => 'dinas',
                'address' => 'Jl. Masjid Raya, Ternate',
                'phone' => '0921-3130123',
                'email' => 'dlh@ternatekota.go.id',
                'website' => 'https://dlh.ternatekota.go.id',
                'head_name' => 'Ir. Sri Wahyuni, M.Si',
                'head_title' => 'Kepala Dinas',
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Dinas Kependudukan dan Pencatatan Sipil',
                'slug' => 'dukcapil-ternate',
                'acronym' => 'DUKCAPIL',
                'description' => 'Dinas yang menyelenggarakan urusan kependudukan dan pencatatan sipil',
                'type' => 'dinas',
                'address' => 'Jl. Revolusi, Ternate',
                'phone' => '0921-3131234',
                'email' => 'dukcapil@ternatekota.go.id',
                'website' => 'https://dukcapil.ternatekota.go.id',
                'head_name' => 'Dra. Fatimah Sari, M.AP',
                'head_title' => 'Kepala Dinas',
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Dinas Pariwisata dan Kebudayaan',
                'slug' => 'disparekbud-ternate',
                'acronym' => 'DISPAREKBUD',
                'description' => 'Dinas yang menyelenggarakan urusan pariwisata dan kebudayaan',
                'type' => 'dinas',
                'address' => 'Jl. Merdeka, Ternate',
                'phone' => '0921-3132345',
                'email' => 'disparekbud@ternatekota.go.id',
                'website' => 'https://disparekbud.ternatekota.go.id',
                'head_name' => 'Drs. Hasan Basri, M.Par',
                'head_title' => 'Kepala Dinas',
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Dinas Sosial',
                'slug' => 'dinsos-ternate',
                'acronym' => 'DINSOS',
                'description' => 'Dinas yang menyelenggarakan urusan sosial dan kesejahteraan',
                'type' => 'dinas',
                'address' => 'Jl. Kartini, Ternate',
                'phone' => '0921-3133456',
                'email' => 'dinsos@ternatekota.go.id',
                'website' => 'https://dinsos.ternatekota.go.id',
                'head_name' => 'Dra. Nurlaila, M.Sos',
                'head_title' => 'Kepala Dinas',
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Badan Perencanaan Pembangunan Daerah',
                'slug' => 'bappeda-ternate',
                'acronym' => 'BAPPEDA',
                'description' => 'Badan yang menyelenggarakan perencanaan pembangunan daerah',
                'type' => 'badan',
                'address' => 'Jl. Sultan Babullah, Ternate',
                'phone' => '0921-3134567',
                'email' => 'bappeda@ternatekota.go.id',
                'website' => 'https://bappeda.ternatekota.go.id',
                'head_name' => 'Dr. Ir. Syamsul Bahri, M.Si',
                'head_title' => 'Kepala Badan',
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Badan Pendapatan Daerah',
                'slug' => 'bapenda-ternate',
                'acronym' => 'BAPENDA',
                'description' => 'Badan yang menyelenggarakan urusan pendapatan daerah',
                'type' => 'badan',
                'address' => 'Jl. Pemuda, Ternate',
                'phone' => '0921-3135678',
                'email' => 'bapenda@ternatekota.go.id',
                'website' => 'https://bapenda.ternatekota.go.id',
                'head_name' => 'Drs. Ahmad Fauzi, M.M',
                'head_title' => 'Kepala Badan',
            ],
            [
                'id' => Str::uuid(),
                'name' => 'DPRD Kota Ternate',
                'slug' => 'dprd-ternate',
                'acronym' => 'DPRD',
                'description' => 'Dewan Perwakilan Rakyat Daerah Kota Ternate',
                'type' => 'dprd',
                'address' => 'Jl. Pahlawan, Ternate',
                'phone' => '0921-3136789',
                'email' => 'dprd@ternatekota.go.id',
                'website' => 'https://dprd.ternatekota.go.id',
                'head_name' => 'H. Muhammad Taher, S.H',
                'head_title' => 'Ketua DPRD',
            ],
        ];

        foreach ($organizations as $organization) {
            $organization['created_at'] = Carbon::now();
            $organization['updated_at'] = Carbon::now();
            DB::table('organizations')->insert($organization);
        }
    }
}

/**
 * Seeder untuk Tags
 */
class TagsSeeder extends Seeder
{
    public function run()
    {
        $tags = [
            ['name' => 'Statistik', 'slug' => 'statistik', 'color' => '#3B82F6'],
            ['name' => 'Demografi', 'slug' => 'demografi', 'color' => '#EF4444'],
            ['name' => 'APBD', 'slug' => 'apbd', 'color' => '#F59E0B'],
            ['name' => 'Pajak Daerah', 'slug' => 'pajak-daerah', 'color' => '#10B981'],
            ['name' => 'Retribusi', 'slug' => 'retribusi', 'color' => '#8B5CF6'],
            ['name' => 'Kesehatan Masyarakat', 'slug' => 'kesehatan-masyarakat', 'color' => '#EF4444'],
            ['name' => 'Fasilitas Umum', 'slug' => 'fasilitas-umum', 'color' => '#6B7280'],
            ['name' => 'Pendidikan Dasar', 'slug' => 'pendidikan-dasar', 'color' => '#10B981'],
            ['name' => 'Pendidikan Menengah', 'slug' => 'pendidikan-menengah', 'color' => '#10B981'],
            ['name' => 'Infrastruktur Jalan', 'slug' => 'infrastruktur-jalan', 'color' => '#6B7280'],
            ['name' => 'Transportasi Publik', 'slug' => 'transportasi-publik', 'color' => '#8B5CF6'],
            ['name' => 'Wisata Bahari', 'slug' => 'wisata-bahari', 'color' => '#06B6D4'],
            ['name' => 'Budaya Lokal', 'slug' => 'budaya-lokal', 'color' => '#F97316'],
            ['name' => 'Bantuan Sosial', 'slug' => 'bantuan-sosial', 'color' => '#EC4899'],
            ['name' => 'Lingkungan Hidup', 'slug' => 'lingkungan-hidup', 'color' => '#059669'],
            ['name' => 'Kualitas Air', 'slug' => 'kualitas-air', 'color' => '#06B6D4'],
            ['name' => 'Pengelolaan Sampah', 'slug' => 'pengelolaan-sampah', 'color' => '#059669'],
            ['name' => 'Kebencanaan', 'slug' => 'kebencanaan', 'color' => '#DC2626'],
            ['name' => 'UMKM', 'slug' => 'umkm', 'color' => '#F59E0B'],
            ['name' => 'Investasi', 'slug' => 'investasi', 'color' => '#F59E0B'],
        ];

        foreach ($tags as $tag) {
            $tagData = [
                'id' => Str::uuid(),
                'name' => $tag['name'],
                'slug' => $tag['slug'],
                'color' => $tag['color'],
                'usage_count' => rand(0, 50),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            DB::table('tags')->insert($tagData);
        }
    }
}

/**
 * Seeder untuk Users (Admin dan Editor)
 */
class UsersSeeder extends Seeder
{
    public function run()
    {
        // Super Admin
        DB::table('users')->insert([
            'id' => Str::uuid(),
            'name' => 'Super Administrator',
            'email' => 'admin@opendata.ternatekota.go.id',
            'password' => bcrypt('admin123'),
            'role' => 'super_admin',
            'status' => 'active',
            'email_verified_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Admin Diskominfo
        $diskominfo = DB::table('organizations')->where('slug', 'diskominfo-ternate')->first();
        if ($diskominfo) {
            DB::table('users')->insert([
                'id' => Str::uuid(),
                'name' => 'Admin Diskominfo',
                'email' => 'admin.diskominfo@ternatekota.go.id',
                'password' => bcrypt('diskominfo123'),
                'organization_id' => $diskominfo->id,
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        // Editor dari berbagai OPD
        $organizations = ['dinkes-ternate', 'disdik-ternate', 'dpupr-ternate', 'bappeda-ternate'];
        
        foreach ($organizations as $orgSlug) {
            $org = DB::table('organizations')->where('slug', $orgSlug)->first();
            if ($org) {
                DB::table('users')->insert([
                    'id' => Str::uuid(),
                    'name' => 'Editor ' . $org->acronym,
                    'email' => 'editor.' . $orgSlug . '@ternatekota.go.id',
                    'password' => bcrypt('editor123'),
                    'organization_id' => $org->id,
                    'role' => 'editor',
                    'status' => 'active',
                    'email_verified_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}

/**
 * Seeder untuk System Settings
 */
class SystemSettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            // General Settings
            [
                'key' => 'site_name',
                'value' => 'Open Data Ternate',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Nama website',
                'is_public' => true,
            ],
            [
                'key' => 'site_description',
                'value' => 'Portal Data Terbuka Pemerintah Kota Ternate',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Deskripsi website',
                'is_public' => true,
            ],
            [
                'key' => 'site_keywords',
                'value' => 'open data, ternate, pemerintah, transparansi, data terbuka',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Keywords untuk SEO',
                'is_public' => true,
            ],
            [
                'key' => 'contact_email',
                'value' => 'opendata@ternatekota.go.id',
                'type' => 'string',
                'group' => 'contact',
                'description' => 'Email kontak utama',
                'is_public' => true,
            ],
            [
                'key' => 'contact_phone',
                'value' => '0921-3121234',
                'type' => 'string',
                'group' => 'contact',
                'description' => 'Telepon kontak utama',
                'is_public' => true,
            ],
            [
                'key' => 'contact_address',
                'value' => 'Jl. Pahlawan Revolusi, Ternate, Maluku Utara 97714',
                'type' => 'string',
                'group' => 'contact',
                'description' => 'Alamat kantor',
                'is_public' => true,
            ],
            
            // API Settings
            [
                'key' => 'api_rate_limit',
                'value' => '1000',
                'type' => 'integer',
                'group' => 'api',
                'description' => 'Rate limit API per jam',
                'is_public' => false,
            ],
            [
                'key' => 'api_key_required',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'api',
                'description' => 'Apakah API key diperlukan',
                'is_public' => false,
            ],
            
            // Upload Settings
            [
                'key' => 'max_file_size',
                'value' => '100', // MB
                'type' => 'integer',
                'group' => 'upload',
                'description' => 'Maksimal ukuran file upload (MB)',
                'is_public' => false,
            ],
            [
                'key' => 'allowed_file_types',
                'value' => '["csv", "xlsx", "json", "pdf", "shp", "geojson"]',
                'type' => 'json',
                'group' => 'upload',
                'description' => 'Tipe file yang diizinkan',
                'is_public' => false,
            ],
            
            // Social Media
            [
                'key' => 'social_facebook',
                'value' => 'https://facebook.com/ternatekota',
                'type' => 'string',
                'group' => 'social',
                'description' => 'URL Facebook',
                'is_public' => true,
            ],
            [
                'key' => 'social_twitter',
                'value' => 'https://twitter.com/ternatekota',
                'type' => 'string',
                'group' => 'social',
                'description' => 'URL Twitter',
                'is_public' => true,
            ],
            [
                'key' => 'social_instagram',
                'value' => 'https://instagram.com/ternatekota',
                'type' => 'string',
                'group' => 'social',
                'description' => 'URL Instagram',
                'is_public' => true,
            ],
            [
                'key' => 'social_youtube',
                'value' => 'https://youtube.com/c/ternatekota',
                'type' => 'string',
                'group' => 'social',
                'description' => 'URL YouTube',
                'is_public' => true,
            ],
            
            // Feature Toggles
            [
                'key' => 'enable_dataset_requests',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'features',
                'description' => 'Enable fitur permintaan dataset',
                'is_public' => false,
            ],
            [
                'key' => 'enable_data_visualization',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'features',
                'description' => 'Enable fitur visualisasi data',
                'is_public' => false,
            ],
            [
                'key' => 'enable_public_api',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'features',
                'description' => 'Enable API publik',
                'is_public' => false,
            ],
            
            // Analytics
            [
                'key' => 'google_analytics_id',
                'value' => '',
                'type' => 'string',
                'group' => 'analytics',
                'description' => 'Google Analytics ID',
                'is_public' => false,
            ],
            [
                'key' => 'enable_analytics',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'analytics',
                'description' => 'Enable analytics tracking',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            $settingData = [
                'id' => Str::uuid(),
                'key' => $setting['key'],
                'value' => $setting['value'],
                'type' => $setting['type'],
                'group' => $setting['group'],
                'description' => $setting['description'],
                'is_public' => $setting['is_public'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            DB::table('system_settings')->insert($settingData);
        }
    }
}