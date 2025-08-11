<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
   public function up(): void
{
    // Aktifkan PostGIS jika belum aktif
    DB::statement('CREATE EXTENSION IF NOT EXISTS postgis');

    Schema::create('mapsets', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
        $table->uuid('uuid')->unique();

        // Info dasar
        $table->string('nama', 255);
        $table->text('deskripsi')->nullable();
        $table->string('gambar')->nullable();
        $table->enum('topic', [
            'Ekonomi',
            'Infrastruktur', 
            'Kemiskinan',
            'Kependudukan',
            'Kesehatan',
            'Lingkungan Hidup',
            'Pariwisata & Kebudayaan',
            'Pemerintah & Desa',
            'Pendidikan',
            'Sosial'
        ])->required();

        // Status
        $table->boolean('is_visible')->default(true);
        $table->boolean('is_active')->default(true);
        $table->integer('views')->default(0);

        $table->timestamps();

        // Kolom geometri
        $table->geometry('geom')->required();

        // Index
        $table->spatialIndex('geom');
    });
}


    public function down(): void
    {
        Schema::dropIfExists('mapsets');
    }
};