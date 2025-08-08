<?php

namespace App\Http\Controllers;

use App\Cache\SectionCache;
use App\Http\Requests\SectionRequest;
use App\Repositories\SectionRepository;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    protected SectionRepository $instance;

    public function __construct(SectionRepository $instance)
    {
        $this->instance = $instance;
    }

    public function fetchSumary(string $cycle_code)
    {
        return response()->json([
            "values" => $this->instance->fetchSumary($cycle_code),
        ]);
    }

    public function create()
    {
        $sectionCache = new SectionCache();
        return response()->json([
            "values" => $sectionCache->fetchByBranch(),
        ]);
    }

    public function store(SectionRequest $request)
    {
        $this->instance->store($request->all());
        $sectionCache = new SectionCache();
        $sectionCache->clear();
        return response()->json([
            "message" => "Sección creada correctamente"
        ]);
    }

    public function update(Request $request, string $section_code)
    {
        $this->instance->changeTutor($section_code, $request->input("tutor"));
        return response()->json([
            "message" => "Sección actualizada correctamente"
        ]);
    }

    public function destroy(string $code)
    {
        $this->instance->destroy($code);
        return response()->json([
            "message" => "Sección eliminada correctamente"
        ]);
    }

    public function fetchByDegree(string $degree_code)
    {
        return response()->json([
            "values" => $this->instance->fetchByDegree($degree_code),
        ]);
    }


}
