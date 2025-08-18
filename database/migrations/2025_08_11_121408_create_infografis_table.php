<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel infografis
        Schema::create('infografis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('slug')->unique();
            
            // Info dasar
            $table->string('nama', 255);
            $table->text('deskripsi')->nullable();
            $table->string('gambar'); // hanya satu file gambar utama
            
            // Kategori/topik
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
            ])->nullable();
            
            // Metadata
            $table->jsonb('data_sources')->nullable();
            $table->text('metodologi')->nullable();
            $table->date('periode_data_mulai')->nullable();
            $table->date('periode_data_selesai')->nullable();
            
            // Tags dan keywords
            $table->jsonb('tags')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true);
            $table->integer('views')->default(0);
            $table->integer('downloads')->default(0);
            
            $table->timestamps();
            
            // Index
            $table->index(['topic', 'is_active']);
            $table->index('tags', null, 'gin'); // PostgreSQL
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('infografis');
    }
};