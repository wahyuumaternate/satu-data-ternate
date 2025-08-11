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
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('uuid')->unique();
            
            // Info dasar
            $table->string('nama', 255);
            $table->text('deskripsi')->nullable();
            $table->string('gambar')->nullable(); // main image/thumbnail
            
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
            
            // Tipe infografis
            $table->enum('tipe', [
                'statistik',
                'timeline',
                'proses',
                'perbandingan',
                'hierarki',
                'geografis',
                'poster',
                'laporan',
                'presentasi'
            ]);
            
            // File dan media
            $table->string('file_path')->nullable(); // file utama (PDF, PNG, JPG)
            $table->string('file_type', 10)->nullable(); // pdf, png, jpg, svg
            $table->unsignedBigInteger('file_size')->nullable(); // dalam bytes
            $table->jsonb('media_files')->nullable(); // file tambahan (images, icons, etc)
            
            // Dimensi dan layout
            $table->enum('orientasi', ['portrait', 'landscape', 'square'])->default('portrait');
            $table->string('ukuran', 20)->nullable(); // A4, A3, custom, social_media, etc
            $table->integer('width')->nullable(); // pixel width
            $table->integer('height')->nullable(); // pixel height
            
            // Content metadata
            $table->jsonb('data_sources')->nullable(); // sumber data yang digunakan
            $table->text('metodologi')->nullable(); // penjelasan metodologi
            $table->date('periode_data_mulai')->nullable();
            $table->date('periode_data_selesai')->nullable();
            
            // Tags dan keywords
            $table->jsonb('tags')->nullable();
            $table->text('keywords')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true);
            $table->integer('views')->default(0);
            $table->integer('downloads')->default(0);
            
            $table->timestamps();
            
            $table->index(['topic', 'is_active']);
            $table->index(['tipe', 'is_public']);
            $table->index(['orientasi', 'ukuran']);
            $table->index('tags', null, 'gin');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infografis');
    }
};