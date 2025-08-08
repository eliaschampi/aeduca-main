<?php

namespace App\Exports;

use App\Repositories\ExpenseRepository;
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

class ExpenseExport implements
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

    private string $fileName = "expenses.xlsx";

    private string $writerType = Excel::XLSX;

    private array $headers = [
        'Content-Type' => 'text/csv',
    ];

    private string $from;

    private string $to;

    public function __construct(string $from, string $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function collection()
    {
        return (new ExpenseRepository())->fetchForExport($this->from, $this->to);
    }

    public function headings(): array
    {
        return [
            "Código",
            "Fecha",
            "Tipo de Gasto",
            "Descripción",
            "Usuario",
            "Nro Comprobante de compra",
            "Total",
            "Código de caja",
            "Detalles"
        ];
    }

    public function map($row): array
    {
        $detail = $row->detail->pluck("description")->toArray();
        return [
            $row->code, //B
            Date::dateTimeToExcel($row->created_at), //C
            $row->actiontype->name, //D
            $row->description, //E
            $row->cash->user->name, //F
            $row->voucher_num, //G
            $row->total, //H
            $row->cash_code,
            implode(",", $detail) //J

        ];
    }

    public function columnFormats(): array
    {
        return [
            "C" => NumberFormat::FORMAT_DATE_DDMMYYYY,
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
