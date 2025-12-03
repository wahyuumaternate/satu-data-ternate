<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DatasetExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $data;
    protected $headers;

    public function __construct(array $data)
    {
        $this->data = $data;
        // Get headers from first row
        $this->headers = !empty($data) ? array_keys($data[0]) : [];
    }

    /**
     * Return collection of data
     */
    public function collection()
    {
        // Convert array to collection with only values
        $rows = [];
        foreach ($this->data as $row) {
            $values = [];
            foreach ($this->headers as $header) {
                $values[] = $row[$header] ?? '';
            }
            $rows[] = $values;
        }

        return collect($rows);
    }

    /**
     * Return headers
     */
    public function headings(): array
    {
        return $this->headers;
    }

    /**
     * Style the headers
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (headers)
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => '1581BF',
                    ],
                ],
                'font' => [
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                    'bold' => true,
                ],
            ],
        ];
    }
}
