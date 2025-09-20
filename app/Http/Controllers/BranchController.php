<?php

namespace App\Http\Controllers;

use App\Http\Requests\BranchRequest;
use App\Repositories\BranchRepository;

class BranchController extends Controller
{
    protected BranchRepository $instance;

    public function __construct(BranchRepository $instance)
    {
        $this->instance = $instance;
    }

    public function index()
    {
        return response()->json([
            "values" => $this->instance->fetchAll()
        ]);
    }

    public function store(BranchRequest $request)
    {
        $this->instance->store($request->all());
        return response()->json([
            "message" => "Correctamente guardado"
        ]);
    }

    public function update(BranchRequest $request, $code)
    {
        $this->instance->update($request->all(), $code);
        return response()->json([
            "message" => "Correctamente actualizado"
        ]);
    }
}
