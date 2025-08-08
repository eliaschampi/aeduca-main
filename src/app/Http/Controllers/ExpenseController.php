<?php

namespace App\Http\Controllers;

use App\Cache\CashCache;
use App\Exports\ExpenseExport;
use App\Http\Requests\ExpenseRequest;
use App\Repositories\ExpenseRepository;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    private ExpenseRepository $instance;


    public function __construct(ExpenseRepository $instance)
    {
        $this->instance = $instance;
    }

    public function show(int $code)
    {
        return response()->json([
            "value" => $this->instance->fetch($code)
        ]);
    }

    public function fetchByDates(string $from, string $to, string $user_code)
    {
        return response()->json([
            "values" => $this->instance->fetchByDates($from, $to, $user_code)
        ]);
    }

    public function store(ExpenseRequest $request)
    {
        $this->instance->store($request->all());
        CashCache::forgetCache();
        return response()->json([
            "message" => "Correctamente guardado"
        ]);
    }

    public function update(Request $request, int $code)
    {
        $this->instance->update($request->all(), $code);
        CashCache::forgetCache();
        return response()->json([
            "message" => "Correctamente actualizado"
        ]);
    }

    public function destroy(int $code)
    {
        $this->instance->destroy($code);
        CashCache::forgetCache();
        return response()->json([
            "message" => "Correctamente eliminado"
        ]);
    }

    public function print(int $code)
    {
        $expense = $this->instance->fetch($code);
        $pdf = \PDF::loadView("pdf.expense", compact("expense"));
        $pdf->setPaper("A4", "portrait");
        return $pdf->download("adj.pdf");
    }

    public function exportToExcel(string $from, string $to)
    {
        return (new ExpenseExport($from, $to));
    }
}
