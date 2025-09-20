<?php

namespace App\Exports;

use App\Repositories\RegisterRepository;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class RegisterExport implements
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

    private string $fileName = "registers.xlsx";

    private string $writerType = Excel::XLSX;

    private array $headers = [
        'Content-Type' => 'text/csv',
    ];

    private string $section_code;

    public function __construct(string $section_code)
    {
        $this->section_code = $section_code;
    }

    public function collection()
    {
        return (new RegisterRepository())->fetchBySection($this->section_code, false);
    }

    public function headings(): array
    {
        return [
            "DNI",
            "Codigo de estudiante",
            "Grupo/Sección",
            "Estudiante",
            "Celular",
            "Estado",
            "Mensualidad"
        ];
    }

    public function map($row): array
    {

        return [
            $row->student_dni, //B
            $row->code, //C
            substr($row->section_code, -2) . " de " . $row->level, //D
            $row->student->person->name . " " . $row->student->person->lastname, //E
            $row->student->person->phone, //F
            $row->state === "a" ? "Activo" : "Finalizó", //G
            $row->monthly //H

        ];
    }

    public function columnFormats(): array
    {
        return [
            "H" => NumberFormat::FORMAT_NUMBER_00
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
