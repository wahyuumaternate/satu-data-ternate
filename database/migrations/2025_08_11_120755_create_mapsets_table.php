<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Aktifkan PostGIS
        DB::statement('CREATE EXTENSION IF NOT EXISTS postgis');

        /**
         * Tabel utama: metadata mapset
         */
        Schema::create('mapsets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('nama', 255);
            $table->text('deskripsi')->nullable();
            $table->string('gambar')->nullable();
            $table->enum('topic', [
                'Ekonomi', 'Infrastruktur', 'Kemiskinan', 'Kependudukan', 'Kesehatan',
                'Lingkungan Hidup', 'Pariwisata & Kebudayaan', 'Pemerintah & Desa',
                'Pendidikan', 'Sosial'
            ]);
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('views')->default(0);
            $table->timestamps();
        });

        /**
         * Tabel detail: geometry + atribut tiap feature
         */
        Schema::create('mapset_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mapset_id')->constrained('mapsets')->cascadeOnDelete();
            $table->jsonb('attributes')->nullable();
            
           
            $table->timestamps();
        });
        // Tambahkan kolom geometri menggunakan PostGIS
         DB::statement('ALTER TABLE mapset_features ADD COLUMN geom GEOMETRY');
        // Index spatial & JSONB
        DB::statement('CREATE INDEX idx_mapset_features_geom ON mapset_features USING GIST (geom)');
        DB::statement('CREATE INDEX idx_mapset_features_attr_gin ON mapset_features USING GIN (attributes jsonb_path_ops)');
    }

    public function down(): void
    {
        Schema::dropIfExists('mapset_features');
        Schema::dropIfExists('mapsets');
    }
};
