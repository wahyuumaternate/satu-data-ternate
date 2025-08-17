<?php

// Buat file: app/Imports/VisualisasiDataImport.php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class VisualisasiDataImport implements ToArray, WithHeadingRow
{
    public function array(array $array): array
    {
        $data = ['labels' => [], 'values' => []];
        
        foreach ($array as $row) {
            // Get first two columns regardless of header names
            $keys = array_keys($row);
            
            if (count($keys) >= 2) {
                $label = trim($row[$keys[0]] ?? '');
                $value = trim($row[$keys[1]] ?? '');
                
                // Skip invalid rows using the same validation as model
                if ($this->isValidDataRow($label, $value)) {
                    $data['labels'][] = $label;
                    $data['values'][] = is_numeric($value) ? (float)$value : 0;
                }
            }
        }
        
        return $data;
    }

    private function isValidDataRow($label, $value): bool
    {
        // Skip empty rows
        if (empty($label) && empty($value)) {
            return false;
        }
        
        // Skip instruction rows (common phrases in instructions)
        $instructionKeywords = [
            'petunjuk',
            'instruction',
            'panduan',
            'cara',
            'hapus',
            'delete',
            'isi data',
            'fill data',
            'contoh',
            'example',
            'sample',
            'template',
            'format',
            'kolom',
            'column',
            'baris',
            'row',
            'penting',
            'important'
        ];
        
        $labelLower = strtolower($label);
        foreach ($instructionKeywords as $keyword) {
            if (strpos($labelLower, $keyword) !== false) {
                return false;
            }
        }
        
        // Skip rows where label looks like instruction numbers (1., 2., etc)
        if (preg_match('/^\d+\./', $label)) {
            return false;
        }
        
        // Skip rows with non-numeric values in value column (except 0)
        if (!is_numeric($value) && $value !== '0' && $value !== 0) {
            return false;
        }
        
        // Skip rows where label is too long (likely instruction)
        if (strlen($label) > 100) {
            return false;
        }
        
        // Skip rows where value is exactly 0 and label contains common template words
        if (($value === 0 || $value === '0') && 
            (strpos($labelLower, 'kategori') !== false || 
             strpos($labelLower, 'nilai') !== false ||
             strpos($labelLower, 'label') !== false)) {
            return false;
        }
        
        return true;
    }
}

