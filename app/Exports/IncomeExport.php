<?php

namespace App\Exports;

use App\Repositories\IncomeDetailRepository;
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

class IncomeExport implements
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

    private string $fileName = "invoices.xlsx";

    private string $writerType = Excel::XLSX;

    private array $headers = [
        'Content-Type' => 'text/csv',
    ];

    private string $from;

    private string $to;

    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function collection()
    {
        $incomeDetailRepository = new IncomeDetailRepository();
        return $incomeDetailRepository->fetchForExport($this->from, $this->to);
    }

    public function headings(): array
    {
        return [
            "Código",
            "Fecha",
            "Usuario",
            "Comprobante",
            "Correlativo",
            "Tipo de Cliente",
            "Razón Social o Estudiante",
            "Modalidad",
            "Concepto",
            "Monto a Pagar",
            "Descuento",
            "Total Importe",
            "Saldo Pendiente"
        ];
    }

    public function map($row): array
    {
        return [
            $row->code,
            Date::dateTimeToExcel($row->income->created_at),
            $row->income->cash->user->name,
            $row->income->type === "03" ? "Boleta de Venta" : "Nota de Venta",
            $row->income->serie,
            $row->income->name["type"],
            $row->income->name["name"],
            $row->actiontype->name,
            $row->title,
            $row->topay,
            $row->discount,
            $row->paid,
            ($row->topay - $row->paid - $row->discount)
        ];
    }

    public function columnFormats(): array
    {
        return [
            "C" => NumberFormat::FORMAT_DATE_DDMMYYYY,
            "K" => NumberFormat::FORMAT_NUMBER_00,
            "L" => NumberFormat::FORMAT_NUMBER_00,
            "M" => NumberFormat::FORMAT_NUMBER_00,
            "N" => NumberFormat::FORMAT_NUMBER_00,
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
