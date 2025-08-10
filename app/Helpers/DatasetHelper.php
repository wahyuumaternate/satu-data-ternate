<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class DatasetHelper
{
    /**
     * Format column name for better display
     */
    public static function formatColumnName($columnName)
    {
        // Replace underscores and hyphens with spaces
        $formatted = str_replace(['_', '-'], ' ', $columnName);
        
        // Convert to title case
        $formatted = ucwords(strtolower($formatted));
        
        // Handle common abbreviations
        $abbreviations = [
            'Id' => 'ID',
            'Url' => 'URL',
            'Api' => 'API',
            'Html' => 'HTML',
            'Css' => 'CSS',
            'Js' => 'JS',
            'Php' => 'PHP',
            'Sql' => 'SQL',
            'Xml' => 'XML',
            'Json' => 'JSON',
            'Pdf' => 'PDF',
            'Csv' => 'CSV'
        ];
        
        foreach ($abbreviations as $search => $replace) {
            $formatted = str_replace($search, $replace, $formatted);
        }
        
        return $formatted;
    }

    /**
     * Detect column data type
     */
    public static function detectDataType($value)
    {
        if (is_null($value) || $value === '') {
            return 'empty';
        }
        
        if (is_numeric($value)) {
            if (is_int($value) || (is_string($value) && ctype_digit($value))) {
                return 'integer';
            } else {
                return 'decimal';
            }
        }
        
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }
        
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return 'url';
        }
        
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
            return 'date';
        }
        
        if (strlen($value) > 100) {
            return 'text';
        }
        
        return 'string';
    }

    /**
     * Format value based on data type
     */
    public static function formatValue($value, $type = null)
    {
        if (is_null($value) || $value === '') {
            return '<span class="text-muted">-</span>';
        }
        
        $type = $type ?? self::detectDataType($value);
        
        switch ($type) {
            case 'integer':
                return '<span class="text-end d-block fw-bold text-primary">' . number_format($value, 0, ',', '.') . '</span>';
                
            case 'decimal':
                return '<span class="text-end d-block fw-bold text-success">' . number_format($value, 2, ',', '.') . '</span>';
                
            case 'email':
                return '<a href="mailto:' . $value . '" class="text-decoration-none">' . $value . '</a>';
                
            case 'url':
                return '<a href="' . $value . '" target="_blank" class="text-decoration-none">' . 
                       '<i class="fas fa-external-link-alt me-1"></i>' . Str::limit($value, 30) . '</a>';
                
            case 'date':
                try {
                    $date = \Carbon\Carbon::parse($value);
                    return '<span class="badge bg-info">' . $date->format('d M Y') . '</span>';
                } catch (\Exception $e) {
                    return $value;
                }
                
            case 'text':
                return '<span class="text-truncate d-block" title="' . htmlspecialchars($value) . '">' . 
                       Str::limit($value, 50) . '</span>';
                
            default:
                return htmlspecialchars($value);
        }
    }

    /**
     * Get column statistics
     */
    public static function getColumnStats($data, $columnKey)
    {
        $values = array_column($data, $columnKey);
        $values = array_filter($values, function($v) { return !is_null($v) && $v !== ''; });
        
        $stats = [
            'total' => count($data),
            'filled' => count($values),
            'empty' => count($data) - count($values),
            'unique' => count(array_unique($values)),
            'type' => 'mixed'
        ];
        
        if (!empty($values)) {
            $firstType = self::detectDataType($values[0]);
            $allSameType = true;
            
            foreach ($values as $value) {
                if (self::detectDataType($value) !== $firstType) {
                    $allSameType = false;
                    break;
                }
            }
            
            $stats['type'] = $allSameType ? $firstType : 'mixed';
            
            // Numeric statistics
            if (in_array($stats['type'], ['integer', 'decimal'])) {
                $numericValues = array_map('floatval', $values);
                $stats['min'] = min($numericValues);
                $stats['max'] = max($numericValues);
                $stats['avg'] = array_sum($numericValues) / count($numericValues);
            }
        }
        
        return $stats;
    }
}
