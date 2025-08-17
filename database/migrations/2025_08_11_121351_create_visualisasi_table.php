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
        // Tabel visualisasi (chart, graph, dashboard)
        Schema::create('visualisasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('uuid')->unique();
            
            // Info dasar
            $table->string('nama', 255);
            $table->text('deskripsi')->nullable();
            
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
            
            // Tipe visualisasi
            $table->enum('tipe', [
                'bar_chart',
                'line_chart', 
                'pie_chart',
                'area_chart',
                'scatter_plot',
                'histogram',
                'heatmap',
                'treemap',
                'dashboard',
                'custom'
            ]);
            
            // Data source
            $table->enum('data_source', ['file','manual'])->default('manual');
            $table->string('source_file')->nullable(); // path file data
           
            
            // Konfigurasi chart
            $table->json('chart_config')->nullable(); // konfigurasi chart (labels, colors, etc)
            $table->json('data_config')->nullable(); // struktur data dan mapping
            $table->json('style_config')->nullable(); // styling tambahan
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true);
            $table->integer('views')->default(0);
            
            $table->timestamps();
            
            $table->index(['topic', 'is_active']);
            $table->index(['tipe', 'is_public']);
            $table->index('data_source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visualisasi');
    }
};