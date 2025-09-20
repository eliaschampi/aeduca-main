<?php

namespace App\Http\Controllers;

use App\Repositories\ScheduleRepository;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    protected ScheduleRepository $instance;

    public function __construct(ScheduleRepository $instance)
    {
        $this->instance = $instance;
    }

    public function fetchMain(string $section_code)
    {
        return response()->json([
            "values" => $this->instance->fetchMain($section_code),
        ]);
    }

    public function fetchByTeacher(string $teacher_dni)
    {
        return response()->json([
            "values" => $this->instance->fetchByTeacher($teacher_dni),
        ]);
    }

    public function store(Request $request)
    {
        $this->instance->store($request->all());
        return response()->json([
            "message" => "Correctamente guardado",
        ]);
    }

    public function update(Request $request, int $code)
    {
        $this->instance->update($request->all(), $code);
        return response()->json([
            "message" => "Correctamente modificado",
        ]);
    }

    public function destroy(int $code)
    {
        $this->instance->destroy($code);
        return response()->json([
            "message" => "Correctamente eliminado",
        ]);
    }
}
