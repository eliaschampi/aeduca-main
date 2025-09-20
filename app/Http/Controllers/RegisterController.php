<?php

namespace App\Http\Controllers;

use App\Cache\RegisterCache;
use App\Exports\LCRExport;
use App\Exports\RegisterExport;
use App\Helpers\RegisterHelper;
use App\Http\Requests\RegisterRequest;
use App\Repositories\RegisterRepository;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    protected RegisterRepository $instance;

    public function __construct(RegisterRepository $instance)
    {
        $this->instance = $instance;
    }

    public function fetchLastRegister(string $dni)
    {
        $register = $this->instance->fetchLatest($dni);
        return response()->json([
            "value" => $register,
        ]);
    }

    public function fetchDebts(string $section_code, string $queryBy)
    {
        $registers = $this->instance->fetchDebts($section_code, $queryBy);
        return response()->json([
            "values" => $registers,
        ]);
    }

    public function fetchByStudent(string $dni)
    {
        return response()->json([
            "values" => $this->instance->fetchByStudent($dni),
        ]);
    }

    public function fetchBySection($section_code, string $inactives)
    {
        return response()->json([
            "values" => $this->instance->fetchBySection($section_code, $inactives === "true"),
        ]);
    }

    public function fetchForAttendance(string $section_code, string $priority)
    {
        $response = $this->instance->fetchForAttendance($section_code, (int) $priority);

        $mapped = $response->map(function ($query) {
            return [
                "entity_identifier" => $query["student_dni"],
                "phone" => $query["student"]["person"]["phone"],
                "state" => "presente",
                "entry_time" => date('H:i'),
                "full_name" => $query["student"]["person"]["name"] . " " . $query["student"]["person"]["lastname"],
            ];
        });
        return response()->json([
            "values" => $mapped,
        ]);
    }

    public function hasOnCache()
    {
        return response()->json(RegisterCache::hasOnCache());
    }

    public function store(RegisterRequest $request)
    {
        $message = "Un estudiante ha sido Matriculado";
        if (!$request->input("invoicing")) {
            if ($request->input("mode") === "Nueva") {
                $this->instance->store($request->all());
            } else {
                $this->instance->update($request->all(), $request->code);
                $message = "Una matricula ha sido actualizado";
            }
        } else {
            RegisterCache::setCache($request->all());
            $message = "Una matrícula esta en proceso.";
        }

        if ($request->input("consV")) {
            return RegisterHelper::consv($request["section_code"], $request["student_name"]);
        }

        return response()->json($message);
    }

    public function toggleState(Request $request, $code)
    {
        $this->instance->toggleState($code, $request->input("state"));
        return response()->json([
            "message" => "Correctamente actualizado",
        ]);
    }

    public function destroy(string $code)
    {
        $this->instance->destroy($code);
        return response()->json([
            "message" => "Correctamente eliminado",
        ]);
    }

    public function exportToExcel(string $section_code)
    {
        return new RegisterExport($section_code);
    }
}
