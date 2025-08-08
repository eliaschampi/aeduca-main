<?php

namespace App\Http\Controllers;

use App\Exports\CashExport;
use App\Interfaces\CashInterface;
use Illuminate\Http\Request;

class CashController extends Controller
{
    protected CashInterface $instance;

    public function __construct(CashInterface $instance)
    {
        $this->instance = $instance;
    }

    public function fetchByMonth($month)
    {
        return response()->json([
            "values" => $this->instance->fetchByMonth($month),
        ]);
    }

    public function fetchSimple()
    {
        return response()->json([
            "value" => $this->instance->fetchSimple(),
        ]);
    }


    public function lastCash()
    {
        return response()->json([
            "value" => $this->instance->lastCash(),
        ]);
    }


    public function fetch(string $user_code, string $date)
    {
        return response()->json([
            "value" => $this->instance->fetch($user_code, $date),
        ]);
    }

    public function fetchChart()
    {
        return response()->json($this->instance->fetchChart());
    }

    public function surrender(Request $request, $cash_code)
    {
        $this->validate($request, [
            "amount" => "required",
        ]);
        $this->instance->surrender($request->all(), $cash_code);
        return response()->json([
            "message" => "Su caja de hoy ha sido rendido"
        ]);
    }

    public function openCash(Request $request)
    {
        $this->validate($request, [
            "cash" => "required",
        ]);
        $this->instance->openCash($request->code, $request->cash);
        return response()->json([
            "message" => "Caja abierta correctamente"
        ]);
    }

    public function toggleCash($code)
    {
        $this->instance->toggleCash($code);
        return response()->json([
            "message" => "Caja actualizado correctamente"
        ]);
    }

    public function exportToExcel()
    {
        return new CashExport();
    }
}
