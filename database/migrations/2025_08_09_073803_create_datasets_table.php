<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('datasets', function (Blueprint $table) {
            $table->id();
             // Tambah kolom approval
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('publish_status');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->after('approval_status');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('approval_notes')->nullable()->after('approved_at');
            $table->text('rejection_reason')->nullable()->after('approval_notes');
            
            
            // Basic Information
            $table->string('title'); // Judul
            $table->string('slug')->unique();
            $table->longText('description'); // Deskripsi
            $table->json('tags'); // Tags
            
            // File Information
            $table->string('filename'); // Nama file yang diupload
            $table->string('original_filename'); // Nama file asli
            $table->string('file_path')->nullable(); // Path file di storage
            $table->bigInteger('file_size')->default(0); // Ukuran file dalam bytes
            $table->string('file_type')->nullable(); // xlsx, xls, csv
            
            // Data Structure
            $table->json('headers'); // Menyimpan nama kolom header
            $table->json('data'); // Menyimpan data dalam format JSON
            $table->integer('total_rows')->default(0); // Total baris data
            $table->integer('total_columns')->default(0); // Total kolom data
            
            // Dataset Metadata
            $table->string('license')->nullable(); // Lisensi
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
            ])->required();
            $table->string('sector')->nullable(); // Bidang
            $table->string('responsible_person')->nullable(); // Penanggung Jawab
            $table->string('contact')->nullable(); // Kontak
            $table->enum('classification', ['publik', 'internal', 'terbatas', 'rahasia']); // Klasifikasi
            $table->enum('status', ['sementara', 'tetap']); // Status Data
            
            // Additional Metadata
            $table->string('data_source')->nullable(); // Sumber Data
            $table->string('data_period')->nullable(); // Periode Data
            $table->string('update_frequency')->nullable(); // Frekuensi Update
            $table->string('geographic_coverage')->nullable(); // Cakupan Geografis
            
            // Status and Publishing
            $table->enum('publish_status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('is_public')->default(true);
            $table->timestamp('published_at')->nullable();
            
            // User and Organization
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User yang upload
            $table->string('organization')->nullable(); // Organisasi
            
            // Additional Fields
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->json('processing_log')->nullable(); // Log proses import
            $table->integer('download_count')->default(0); // Jumlah download
            $table->integer('view_count')->default(0); // Jumlah view
            
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'classification']);
            $table->index(['topic', 'sector']);
            $table->index('publish_status');
            $table->index('created_at');
            // Index untuk performa
            $table->index(['approval_status', 'created_at']);
            $table->index('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datasets');
    }
};