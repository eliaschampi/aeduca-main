<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\OpRequest;
use App\Repositories\OpRepository;

class OpController extends Controller
{
    private OpRepository $instance;

    public function __construct(OpRepository $instance)
    {
        $this->instance = $instance;
    }

    public function show(string $section_code)
    {
        return response()->json([
            "values" => $this->instance->fetchBySection($section_code),
        ]);
    }

    public function store(OpRequest $request)
    {
        $this->instance->store($request->all());
        return response()->json([
            "message" => "Correctamente guardado",
        ]);
    }

    public function update(Request $request, string $code)
    {
        $this->instance->update((int) $code, $request->all());
        return response()->json([
            "message" => "Correctamente modificado",
        ]);
    }

    public function destroy(string $code)
    {
        $this->instance->destroy($code);
        return response()->json([
            "message" => "Correctamente eliminado",
        ]);
    }
}
