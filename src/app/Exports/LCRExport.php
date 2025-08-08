<?php

namespace App\Exports;

use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class LCRExport implements
    FromCollection,
    WithMapping,
    WithHeadings,
    WithColumnFormatting,
    ShouldAutoSize,
    Responsable,
    WithCustomStartCell,
    WithStyles
{
    use Exportable;

    private string $fileName = "lcr.xlsx";

    private string $writerType = Excel::XLSX;

    private array $headers = [
        'Content-Type' => 'text/csv',
    ];

    private int $lesson_code;

    private string $section_code;

    public function __construct($lesson_code, $section_code)
    {
        $this->lesson_code = $lesson_code;
        $this->section_code = $section_code;
    }

    public function collection()
    {
        return (new \App\Repositories\RegisterRepository())
            ->fetchWithLCRbySC($this->lesson_code, $this->section_code);
    }

    public function headings(): array
    {
        return [
            "Código",
            "DNI",
            "Nivel",
            "Grupo/Grado",
            "Estudiante",
            "Nota",
        ];
    }

    public function map($row): array
    {

        return [
            $row->code, //B
            $row->student_dni, //C
            $row->level,
            substr($row->section_code, -2), //E
            $row->student->person->name . " " . $row->student->person->lastname, //F
            $row->score, //G

        ];
    }

    public function columnFormats(): array
    {
        return [
            "G" => NumberFormat::FORMAT_NUMBER_00,
        ];
    }

    public function styles($sheet): array
    {
        return [
            2 => ['font' => ['bold' => true]],
        ];
    }

    public function startCell(): string
    {
        return 'B2';
    }
}
