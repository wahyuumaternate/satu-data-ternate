<?php

namespace App\Imports;

use App\Models\Dataset;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;

class DynamicImport implements ToArray, WithHeadingRow, WithChunkReading, WithValidation
{
    use Importable;

    protected $filename;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Import data from Excel array
     */
    public function array(array $rows): void
    {
        // Validasi data tidak kosong
        if (empty($rows)) {
            throw new \InvalidArgumentException('File Excel tidak memiliki data atau format tidak valid.');
        }

        // Ambil header dari baris pertama
        $rawHeaders = array_keys($rows[0]);
        
        // Validasi header tidak kosong
        if (empty($rawHeaders)) {
            throw new \InvalidArgumentException('File Excel tidak memiliki kolom header yang valid.');
        }

        // Debug: Log raw headers
        Log::info('Raw Headers:', $rawHeaders);
        
        // Bersihkan dan format header
        $cleanHeaders = $this->formatHeaders($rawHeaders);
        
        // Restrukturisasi data agar konsisten dengan header
        $restructuredData = $this->restructureData($rows, $rawHeaders, $cleanHeaders);
        
        // Bersihkan data kosong
        $cleanData = $this->cleanData($restructuredData);

        // Debug: Log final data structure
        Log::info('Clean Headers:', $cleanHeaders);
        Log::info('Sample Data:', array_slice($cleanData, 0, 2));

        // Simpan ke database
        Dataset::create([
            'filename' => $this->filename,
            'headers' => $cleanHeaders,
            'data' => $cleanData,
            'total_rows' => count($cleanData)
        ]);
    }

    /**
     * Restrukturisasi data agar sesuai dengan urutan header yang benar
     */
    private function restructureData(array $rows, array $rawHeaders, array $cleanHeaders): array
    {
        $restructuredRows = [];
        
        foreach ($rows as $rowIndex => $row) {
            $newRow = [];
            
            // Pastikan setiap header memiliki nilai yang sesuai
            foreach ($rawHeaders as $index => $rawHeader) {
                $cleanHeader = $cleanHeaders[$index] ?? 'Column ' . ($index + 1);
                
                // Ambil nilai dari row berdasarkan key yang sesuai
                $value = $row[$rawHeader] ?? null;
                
                // Bersihkan nilai
                if (is_string($value)) {
                    $value = trim($value);
                    if ($value === '') {
                        $value = null;
                    }
                }
                
                $newRow[$cleanHeader] = $value;
            }
            
            $restructuredRows[] = $newRow;
        }
        
        return $restructuredRows;
    }

    /**
     * Format dan bersihkan header kolom
     */
    private function formatHeaders(array $headers): array
    {
        return array_map(function($header, $index) {
            // Jika header kosong atau null, beri nama default
            if (empty($header) || is_null($header)) {
                return 'Column ' . ($index + 1);
            }
            
            // Hapus karakter khusus dan ganti dengan spasi
            $clean = trim(str_replace(['_', '-', '.', '/'], ' ', $header));
            
            // Hapus multiple spaces
            $clean = preg_replace('/\s+/', ' ', $clean);
            
            // Konversi ke Title Case
            $clean = Str::title($clean);
            
            // Handle singkatan umum
            $abbreviations = [
                'Id' => 'ID',
                'Url' => 'URL',
                'Api' => 'API',
                'Html' => 'HTML',
                'Pdf' => 'PDF',
                'Csv' => 'CSV',
                'Json' => 'JSON',
                'Xml' => 'XML',
                'Opd' => 'OPD',
                'No' => 'No'
            ];
            
            foreach ($abbreviations as $search => $replace) {
                // Handle whole word replacement
                $clean = preg_replace('/\b' . $search . '\b/', $replace, $clean);
            }
            
            // Jika masih kosong setelah cleaning, beri nama default
            if (empty($clean)) {
                return 'Column ' . ($index + 1);
            }
            
            return $clean;
        }, $headers, array_keys($headers));
    }

    /**
     * Bersihkan data dari baris kosong dan null
     */
    private function cleanData(array $rows): array
    {
        return array_filter($rows, function($row) {
            // Remove rows yang semua kolomnya kosong atau null
            $nonEmptyValues = array_filter($row, function($value) {
                return !is_null($value) && trim($value) !== '' && $value !== '-';
            });
            
            // Setidaknya harus ada 1 kolom yang berisi data
            return count($nonEmptyValues) > 0;
        });
    }

    /**
     * Validasi data sebelum import
     */
    public function rules(): array
    {
        return [
            // Bisa ditambahkan validasi spesifik di sini
        ];
    }

    /**
     * Set chunk size untuk file besar
     */
    public function chunkSize(): int
    {
        return 500; // Kurangi chunk size untuk handling yang lebih baik
    }

    /**
     * Custom error messages
     */
    public function customValidationMessages(): array
    {
        return [
            'required' => 'Kolom :attribute tidak boleh kosong.',
            'email' => 'Format email pada kolom :attribute tidak valid.',
            'numeric' => 'Kolom :attribute harus berupa angka.',
        ];
    }

    /**
     * Transform heading row
     */
    public function transformHeading($value, $key)
    {
        // Pastikan heading tidak kosong
        if (empty($value)) {
            return 'column_' . $key;
        }
        
        // Kembalikan heading asli untuk diproses di formatHeaders
        return $value;
    }
}