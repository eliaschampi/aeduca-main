<?php

namespace App\Http\Controllers;

use App\Cache\ConfigCache;
use App\Repositories\CashActionTypeRepository;
use Illuminate\Http\Request;

class CashActionTypeController extends Controller
{
    private CashActionTypeRepository $instance;

    public function __construct(CashActionTypeRepository $instance)
    {
        $this->instance = $instance;
    }

    public function show(string $mode)
    {
        $name = "car" . $mode;
        $res = ConfigCache::catCache($name, fn () => $this->instance->fetchByMode($mode));
        return response()->json([
            "values" => $res
        ]);
    }

    public function store(Request $request)
    {
        $value = $this->instance->store($request->all());
        return response()->json([
            "message" => "correctamente guardado",
            "value" => $value,
        ]);
    }

    public function update(Request $request, $code)
    {
        $this->instance->update($request->all(), $code);
        return response()->json([
            "message" => "correctamente actualizado",
        ]);
    }
}
