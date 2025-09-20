<?php

namespace App\Http\Controllers;

use App\Cache\ConfigCache;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function fetchCycleTypes()
    {
        return response()->json([
            "values" => config("main.cycle"),
        ]);
    }

    public function fetchConfig()
    {
        return response()->json([
            "values" => ConfigCache::getCoreConfig(),
        ]);
    }

    public function fetchCompany()
    {
        return response()->json([
            "values" => ConfigCache::getCompanyData(),
        ]);
    }

    public function updateConfig(Request $request, string $key)
    {
        ConfigCache::updateConfig($request->all(), $key);
        return response()->json([
            "message" => "Correctamente guardado"
        ]);
    }
}
