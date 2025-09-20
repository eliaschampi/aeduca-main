<?php

namespace App\Http\Controllers;

use App\Exports\IncomeExport;
use App\Http\Requests\IncomeRequest;
use App\Interfaces\IncomeInterface;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    private IncomeInterface $instance;

    public function __construct(IncomeInterface $instance)
    {
        $this->instance = $instance;
    }

    public function fetchByDates(string $from, string $to, string $user_code)
    {
        return response()->json([
            "values" => $this->instance->fetchByDates($from, $to, $user_code),
        ]);
    }

    public function canceleds()
    {
        return response()->json([
            "values" => $this->instance->canceleds(),
        ]);
    }

    public function store(IncomeRequest $request)
    {
        try {
            $response = $this->instance->store($request->all());

            return response()->json([
                "message" => "Operación exitosa!",
                "income" => $response,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                "message" => $ex->getMessage(),
            ], 500);
        }
    }

    public function canceled(Request $request, $code)
    {
        $this->instance->canceled($request->all(), $code);
        return response()->json([
            "message" => "Anulado correctamente",
        ]);
    }

    public function exportToExcel($from, $to)
    {
        return new IncomeExport($from, $to);
    }

    public function printTicketPdf(Request $request)
    {
        $income = $request->get("income");
        $details = $request->get("details");
        $pdf = \PDF::loadView("pdf.ticket", compact("income", "details"));
        $pdf->setPaper("A4", "portrait");
        return $pdf->download("adj.pdf");
    }
}
