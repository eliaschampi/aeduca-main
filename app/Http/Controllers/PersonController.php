<?php

namespace App\Http\Controllers;

use App\Helpers\MainHelper;
use App\Repositories\PersonRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    protected PersonRepository $instance;

    public function __construct(PersonRepository $instance)
    {
        $this->instance = $instance;
    }

    public function show(string $dni)
    {
        return response()->json([
            "value" => $this->instance->fetchFullName($dni),
        ]);
    }

    public function fetchByDNI(string $type, string $dni)
    {
        try {
            return response()->json([
                "value" => $this->instance->fetch($type, $dni),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "message" => "No hay resultados para el dni",
            ], 404);
        }
    }

    public function fetchSimilars(string $fullname)
    {
        return response()->json([
            "values" => $this->instance->fetchSimilars(urldecode($fullname)),
        ]);
    }
    //changeDNI
    public function update(Request $request, string $dni)
    {
        $res = $this->instance->changeDNI($dni, $request->input("newdni"));
        if (!$res) {
            return response()->json([
                "message" => "DNI ya está en uso",
            ], 422);
        }
        return response()->json([
            "message" => "Correctamente actualizado",
        ]);
    }

    public function destroy(string $dni)
    {
        $person = $this->instance->fetchSingle($dni);

        $deletedfile = true;

        if ($person->profile !== null) {
            $deletedfile = MainHelper::deleteImage($person->profile->image);
        }

        $deleted = $this->instance->destroy($person);

        if ($deletedfile === false || $deleted === false) {
            return response()->json([
                "message" => "Error al eliminar la imagen",
            ], 500);
        }

        return response()->json([
            "message" => "Correctamente eliminado"
        ]);
    }
}
