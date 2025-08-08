<?php

namespace App\Http\Controllers;

use App\Cache\CycleCache;
use App\Http\Requests\CycleRequest;
use App\Repositories\CycleRepository;
use App\Repositories\RegisterRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CycleController extends Controller
{
    protected CycleRepository $instance;

    public function __construct(CycleRepository $instance)
    {
        $this->instance = $instance;
    }

    public function show(string $code)
    {
        return response()->json([
            "value" => $this->instance->fetchByCode($code),
        ]);
    }

    public function fetchActives()
    {
        return response()->json([
            "values" => $this->instance->fetchActives(),
        ]);
    }

    public function fetchCycles(int $branch_code = 0)
    {
        return response()->json([
            "values" => $this->instance->fetchCycles($branch_code),
        ]);
    }

    public function fetchLatestCode(string $cycle_type)
    {
        return response()->json([
            "value" => $this->instance->fetchLatestCode($cycle_type)
        ]);
    }

    public function fetchAttVars(string $code)
    {
        $cacheds = CycleCache::attendanceVariablesShouldBeCached($code);
        return response()->json([
            "values" => $cacheds,
        ]);
    }

    public function store(CycleRequest $request)
    {
        try {
            DB::beginTransaction();
            $cycle = $this->instance->store($request->all());

            $s = $request->input("d_a");
            $e = $request->input("d_b");

            for ($i = $s; $i <= $e; $i++) {
                $cycle->degrees()->create([
                    "code" => $cycle->code . $i,
                    "cycle_code" => $cycle->code,
                ]);
            }

            DB::commit();
            return response()->json([
                "message" => "Nivel Académico correctamente aperturado"
            ]);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json([
                "message" => $ex->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, string $code)
    {
        $this->instance->update($request->all(), $code);
        CycleCache::forgetAttendanceVariables($code);
        return response()->json([
            "message" => "Modificado correctamente",
        ], 200);
    }

    public function finalize(Request $request, string $code)
    {
        $this->instance->finalize($code);
        (new RegisterRepository())->finalizeAll($code);
        return response()->json([
            "message" => "Finalizado correctamente",
        ], 200);
    }
}
