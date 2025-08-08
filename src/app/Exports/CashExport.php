<?php

namespace App\Exports;

use App\Repositories\CashRepository;
use Carbon\Carbon;
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
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CashExport implements
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

    private string $fileName = "cashes.xlsx";

    private string $writerType = Excel::XLSX;

    private array $headers = [
        'Content-Type' => 'text/csv',
    ];

    public function collection()
    {
        return (new CashRepository())->fetchByMonth("all");
    }


    public function headings(): array
    {
        return [
            "Codigo",
            "Fecha",
            "Ingresos",
            "Egresos",
            "Rendido",
            "Descripción Rendido",
            "Saldo del día"
        ];
    }

    public function map($row): array
    {
        return [
            $row->code,
            Date::dateTimeToExcel(Carbon::parse($row->created_at)),
            $row->isum,
            $row->esum,
            empty($row->surrendered) ? "0.00" : $row->surrendered->amount,
            empty($row->surrendered) ? "" : $row->surrendered->recipient,
            $row->cash
        ];
    }

    public function columnFormats(): array
    {
        return [
            "C" => NumberFormat::FORMAT_DATE_DDMMYYYY,
            "D" => NumberFormat::FORMAT_NUMBER_00,
            "E" => NumberFormat::FORMAT_NUMBER_00,
            "F" => NumberFormat::FORMAT_NUMBER_00,
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
