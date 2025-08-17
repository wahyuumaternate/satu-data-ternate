<?php

namespace App\Models;

use App\Imports\VisualisasiDataImport;
use Carbon\Exceptions\Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class Visualisasi extends Model
{
    use HasFactory;

    protected $table = 'visualisasi';

    protected $fillable = [
        'user_id',
        'uuid',
        'nama',
        'deskripsi',
        'topic',
        'tipe',
        'data_source',
        'source_file',
        'chart_config',
        'data_config',
        'style_config',
        'is_active',
        'is_public',
        'views'
    ];

    protected $casts = [
        'chart_config' => 'array',
        'data_config' => 'array',
        'style_config' => 'array',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'views' => 'integer'
    ];

    // Boot method untuk generate UUID
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
    }

    // Relationship dengan User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessor untuk mendapatkan label topic
    public function getTopicLabelAttribute(): string
    {
        $topics = [
            'Ekonomi' => 'Ekonomi',
            'Infrastruktur' => 'Infrastruktur',
            'Kemiskinan' => 'Kemiskinan',
            'Kependudukan' => 'Kependudukan',
            'Kesehatan' => 'Kesehatan',
            'Lingkungan Hidup' => 'Lingkungan Hidup',
            'Pariwisata & Kebudayaan' => 'Pariwisata & Kebudayaan',
            'Pemerintah & Desa' => 'Pemerintah & Desa',
            'Pendidikan' => 'Pendidikan',
            'Sosial' => 'Sosial'
        ];

        return $topics[$this->topic] ?? $this->topic;
    }

    // Accessor untuk mendapatkan label tipe
    public function getTipeLabelAttribute(): string
    {
        $types = [
            'bar_chart' => 'Bar Chart',
            'line_chart' => 'Line Chart',
            'pie_chart' => 'Pie Chart',
            'area_chart' => 'Area Chart',
            'scatter_plot' => 'Scatter Plot',
            'histogram' => 'Histogram',
            'heatmap' => 'Heatmap',
            'treemap' => 'Treemap',
            'dashboard' => 'Dashboard',
            'custom' => 'Custom'
        ];

        return $types[$this->tipe] ?? $this->tipe;
    }

    // Accessor untuk mendapatkan badge class berdasarkan topic
    public function getTopicBadgeClassAttribute(): string
    {
        $classes = [
            'Ekonomi' => 'badge-success',
            'Infrastruktur' => 'badge-primary',
            'Kemiskinan' => 'badge-warning',
            'Kependudukan' => 'badge-info',
            'Kesehatan' => 'badge-danger',
            'Lingkungan Hidup' => 'badge-success',
            'Pariwisata & Kebudayaan' => 'badge-secondary',
            'Pemerintah & Desa' => 'badge-dark',
            'Pendidikan' => 'badge-primary',
            'Sosial' => 'badge-info'
        ];

        return $classes[$this->topic] ?? 'badge-secondary';
    }

    // Scope untuk filter berdasarkan topic
    public function scopeByTopic($query, $topic)
    {
        return $query->where('topic', $topic);
    }

    // Scope untuk filter berdasarkan tipe
    public function scopeByTipe($query, $tipe)
    {
        return $query->where('tipe', $tipe);
    }

    // Scope untuk hanya visualisasi yang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk hanya visualisasi yang public
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    // Method untuk increment views
    public function incrementViews()
    {
        $this->increment('views');
    }

    // Static method untuk mendapatkan daftar topics
    public static function getTopics(): array
    {
        return [
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
        ];
    }

    // Static method untuk mendapatkan daftar tipe
    public static function getTipes(): array
    {
        return [
            'bar_chart' => 'Bar Chart',
            'line_chart' => 'Line Chart',
            'pie_chart' => 'Pie Chart',
            'area_chart' => 'Area Chart',
            'scatter_plot' => 'Scatter Plot',
            'histogram' => 'Histogram',
            'heatmap' => 'Heatmap',
            'treemap' => 'Treemap',
            'dashboard' => 'Dashboard',
            'custom' => 'Custom'
        ];
    }

    // Method untuk mendapatkan data yang sudah diproses
    public function getProcessedData(): array
    {
        if ($this->data_source === 'manual' && $this->data_config) {
            return $this->data_config['data'] ?? [];
        }

        if ($this->data_source === 'file' && $this->source_file) {
            return $this->processFileData();
        }

        return [];
    }

   

    // Method untuk memproses file CSV
    private function processCsvFile(string $filePath): array
    {
        $data = ['labels' => [], 'values' => []];
        
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            $headers = fgetcsv($handle, 1000, ",");
            
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($row) >= 2) {
                    $data['labels'][] = $row[0];
                    $data['values'][] = is_numeric($row[1]) ? (float)$row[1] : 0;
                }
            }
            fclose($handle);
        }
        
        return $data;
    }

    // Method untuk memproses file Excel (membutuhkan library PhpSpreadsheet)
    private function processExcelFile(string $filePath): array
    {
        // Implementasi ini membutuhkan PhpSpreadsheet
        // composer require phpoffice/phpspreadsheet
        
        $data = ['labels' => [], 'values' => []];
        
        try {
            if (class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();
                
                // Skip header row
                for ($i = 1; $i < count($rows); $i++) {
                    if (isset($rows[$i][0]) && isset($rows[$i][1])) {
                        $data['labels'][] = $rows[$i][0];
                        $data['values'][] = is_numeric($rows[$i][1]) ? (float)$rows[$i][1] : 0;
                    }
                }
            }
        } catch (Exception $e) {
            // Log error atau handle sesuai kebutuhan
        }
        
        return $data;
    }

    // Method untuk mendapatkan file URL yang bisa diakses public
    public function getFileUrlAttribute(): ?string
    {
        if ($this->source_file) {
            return asset('storage/' . $this->source_file);
        }
        return null;
    }

    // Method untuk check apakah file exists
    public function fileExists(): bool
    {
        if (!$this->source_file) {
            return false;
        }
        
        return file_exists(storage_path('app/public/' . $this->source_file));
    }

    // Method untuk mendapatkan file size
    public function getFileSizeAttribute(): ?string
    {
        if (!$this->source_file || !$this->fileExists()) {
            return null;
        }
        
        $bytes = filesize(storage_path('app/public/' . $this->source_file));
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    // baru
    
// Method untuk memproses data dari file
private function processFileData(): array
{
    $filePath = storage_path('app/public/' . $this->source_file);
    
    if (!file_exists($filePath)) {
        return [];
    }

    try {
        // Gunakan Maatwebsite/Excel untuk import
        $importedData = Excel::toArray(new VisualisasiDataImport, $filePath);
        
        // Get the first sheet data
        if (!empty($importedData[0])) {
            return $this->processImportedData($importedData[0]);
        }
        
        return [];
        
    } catch (\Exception $e) {
        // Log error
        // Log::error('Error processing file: ' . $e->getMessage());
        return [];
    }
}

// Method untuk memproses data yang sudah diimport
private function processImportedData($importedData): array
{
    $data = ['labels' => [], 'values' => []];
    
    foreach ($importedData as $row) {
        // Ambil 2 kolom pertama
        $keys = array_keys($row);
        
        if (count($keys) >= 2) {
            $label = trim($row[$keys[0]] ?? '');
            $value = trim($row[$keys[1]] ?? '');
            
            // Validasi data
            if ($this->isValidDataRow($label, $value)) {
                $data['labels'][] = $label;
                $data['values'][] = is_numeric($value) ? (float)$value : 0;
            }
        }
    }
    
    return $data;
}

// Method validasi tetap sama seperti sebelumnya
private function isValidDataRow($label, $value): bool
{
    // Skip empty rows
    if (empty($label) && empty($value)) {
        return false;
    }
    
    // Skip instruction rows
    $instructionKeywords = [
        'petunjuk', 'instruction', 'panduan', 'cara', 'hapus', 'delete',
        'isi data', 'fill data', 'contoh', 'example', 'sample', 'template',
        'format', 'kolom', 'column', 'baris', 'row', 'penting', 'important'
    ];
    
    $labelLower = strtolower($label);
    foreach ($instructionKeywords as $keyword) {
        if (strpos($labelLower, $keyword) !== false) {
            return false;
        }
    }
    
    // Skip numbered instructions
    if (preg_match('/^\d+\./', $label)) {
        return false;
    }
    
    // Skip non-numeric values
    if (!is_numeric($value) && $value !== '0' && $value !== 0) {
        return false;
    }
    
    // Skip long labels (likely instructions)
    if (strlen($label) > 100) {
        return false;
    }
    
    // Skip template headers with 0 values
    if (($value === 0 || $value === '0') && 
        (strpos($labelLower, 'kategori') !== false || 
         strpos($labelLower, 'nilai') !== false ||
         strpos($labelLower, 'label') !== false)) {
        return false;
    }
    
    return true;
}
}