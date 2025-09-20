<?php

namespace App\Exports;

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

class AttendanceBySectionExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    Responsable,
    WithColumnFormatting,
    WithStyles,
    WithCustomStartCell
{
    use Exportable;

    private string $fileName = "attendance.xlsx";

    private string $writerType = Excel::XLSX;

    private array $headers = [
        'Content-Type' => 'text/csv',
    ];

    private string $section_code;

    private string $date;

    private int $priority;

    public function __construct(string $section_code, string $date, int $priority)
    {
        $this->section_code = $section_code;
        $this->date = $date;
        $this->priority = $priority;
    }

    public function collection()
    {
        return (new \App\Repositories\AttendanceRepository())->fetchBySectionAndDate(
            $this->section_code,
            $this->date,
            $this->priority
        );
    }

    public function headings(): array
    {
        return [
            "Estudiante",
            "Celular",
            "Ingreso nro",
            "Hora de ingreso",
            "Estado",
            "Justificación",
        ];
    }

    public function map($row): array
    {
        return [
            $row->person->name . " " . $row->person->lastname,
            $row->person->phone,
            $row->priority,
            $row->entry_time,
            $row->state,
            empty($row->justification) ? "" : "Envió justificación",
        ];
    }

    public function styles($sheet): array
    {
        return [
            2 => ["font" => ["bold" => true]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            "E" => NumberFormat::FORMAT_DATE_TIME1,
        ];
    }

    public function startCell(): string
    {
        return "B2";
    }
}
