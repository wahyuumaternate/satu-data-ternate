<?php

// Buat file: app/Exports/VisualisasiTemplateExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class VisualisasiTemplateExport implements WithMultipleSheets
{
    protected $template;

    public function __construct($template)
    {
        $this->template = $template;
    }

    public function sheets(): array
    {
        return [
            'Data Template' => new DataTemplateSheet($this->template),
            'Petunjuk' => new InstructionSheet($this->template),
        ];
    }
}

// Sheet untuk Data Template
class DataTemplateSheet implements FromArray, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected $template;

    public function __construct($template)
    {
        $this->template = $template;
    }

    public function array(): array
    {
        return $this->template['sample_data'];
    }

    public function headings(): array
    {
        return $this->template['headers'];
    }

    public function title(): string
    {
        return 'Data Template';
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk header
        $sheet->getStyle('A1:' . chr(65 + count($this->template['headers']) - 1) . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['argb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => '4472C4'],
            ],
        ]);

        // Style untuk data
        $lastRow = count($this->template['sample_data']) + 1;
        $sheet->getStyle('A2:' . chr(65 + count($this->template['headers']) - 1) . $lastRow)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'F2F2F2'],
            ],
        ]);

        return $sheet;
    }
}

// Sheet untuk Petunjuk
class InstructionSheet implements FromArray, WithTitle, WithStyles, ShouldAutoSize
{
    protected $template;

    public function __construct($template)
    {
        $this->template = $template;
    }

    public function array(): array
    {
        return [
            ['PETUNJUK PENGGUNAAN TEMPLATE'],
            [''],
            ['1. Gunakan sheet "Data Template" untuk mengisi data'],
            ['2. HAPUS SEMUA data contoh di sheet "Data Template"'],
            ['3. Isi data Anda mulai dari baris 2 (setelah header)'],
            ['4. Format data:'],
            ['   - Kolom A: ' . $this->template['headers'][0] . ' (teks)'],
            ['   - Kolom B: ' . $this->template['headers'][1] . ' (angka)'],
            ['5. Jangan ada sel kosong di tengah data'],
            ['6. Jangan mengubah nama header'],
            ['7. Simpan file dan upload ke sistem'],
            [''],
            ['PENTING:'],
            ['- Hapus semua data contoh sebelum mengisi data Anda'],
            ['- Pastikan hanya ada data valid yang tersisa'],
            ['- Format: ' . $this->template['headers'][0] . ' | ' . $this->template['headers'][1]],
            [''],
            ['CONTOH DATA YANG BENAR:'],
            $this->template['headers'],
            ...$this->template['sample_data']
        ];
    }

    public function title(): string
    {
        return 'Petunjuk';
    }

    public function styles(Worksheet $sheet)
    {
        // Title
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['argb' => 'DC2626'],
            ],
        ]);

        // PENTING section
        $sheet->getStyle('A13')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['argb' => 'DC2626'],
            ],
        ]);

        // CONTOH DATA section
        $sheet->getStyle('A18')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['argb' => '059669'],
            ],
        ]);

        // Header contoh
        $sheet->getStyle('A19:B19')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'E5E7EB'],
            ],
        ]);

        return $sheet;
    }
}

