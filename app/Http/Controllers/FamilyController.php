<?php

namespace App\Http\Controllers;

use App\Http\Requests\PersonRequest;
use App\Repositories\FamilyRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FamilyController extends Controller
{
    private FamilyRepository $instance;

    public function __construct(FamilyRepository $instance)
    {
        $this->instance = $instance;
    }

    public function show(string $dni)
    {
        try {
            return response()->json([
                "value" => $this->instance->fetch($dni),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "message" => "No hay resultados para el dni",
            ], 404);
        }
    }

    public function self(string $dni)
    {
        return response()->json([
            "value" => $this->instance->self($dni),
        ]);
    }

    public function fetchBySection(string $section_code)
    {
        return response()->json([
            "values" => $this->instance->fetchBySection($section_code),
        ]);
    }

    public function search(string $name)
    {
        return response()->json([
            "values" => $this->instance->search($name),
        ]);
    }

    public function store(PersonRequest $request)
    {
        $this->instance->store($request->all());
        return response()->json([
            "message" => "Correctamente guardado",
        ]);
    }

    public function update(PersonRequest $request, string $dni)
    {
        $this->instance->update($request->all(), $dni);
        return response()->json([
            "message" => "Correctamente actualizado",
        ]);
    }

    public function fetchByStudent(string $student_dni)
    {
        return response()->json([
            "values" => $this->instance->fetchByStudent($student_dni),
        ]);
    }

    public function addStudent(Request $request)
    {
        $validated = $request->validate([
            "family_dni" => "required",
            "student_dni" => ["required", Rule::unique("family_student")->where("family_dni", $request->family_dni)],
            "relation_type" => "required",
            "is_main" => "required",
        ]);
        $this->instance->addStudent(
            $validated["family_dni"],
            $validated["student_dni"],
            $validated["relation_type"],
            $validated["is_main"],
        );
        return response()->json([
            "message" => "Correctamente agregado",
        ]);
    }

    public function removeStudent(string $family_dni, string $student_dni)
    {
        $this->instance->removeStudent($family_dni, $student_dni);
        return response()->json([
            "message" => "Correctamente actualizado",
        ]);
    }

    public function fetchStudents(string $family_dni)
    {
        return response()->json([
            "values" => $this->instance->fetchStudents($family_dni),
        ]);
    }
}
