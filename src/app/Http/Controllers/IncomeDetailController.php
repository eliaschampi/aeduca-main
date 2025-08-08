<?php

namespace App\Http\Controllers;

use App\Cache\IncomeCache;
use App\Cache\RegisterCache;
use App\Http\Requests\IncomeDetailRequest;
use App\Repositories\IncomeDetailRepository;

class IncomeDetailController extends Controller
{
    private IncomeDetailRepository $instance;

    public function __construct(IncomeDetailRepository $instance)
    {
        $this->instance = $instance;
    }

    public function fetchByIncome($code)
    {
        return response()->json([
            "values" => $this->instance->fetchByIncome($code)
        ]);
    }

    public function fetchPaids($register_code)
    {
        return response()->json([
            "values" => $this->instance->fetchPaids($register_code)
        ]);
    }

    public function showFromCache()
    {
        return response()->json([
            "values" => IncomeCache::fetchFromCache(),
            "register" => RegisterCache::fetchFromCache()
        ]);
    }

    public function storeInCache(IncomeDetailRequest $request)
    {
        $cachedValues = IncomeCache::fetchFromCache();

        $cachedValues[] = [
            "id" => rand(1000, 9999),
            "actiontype" => $request->input("actiontype"),
            "title" => $request->input("title"),
            "topay" => $request->input("topay"),
            "discount" => $request->input("discount"),
            "paid" => $request->input("paid"),
            "pending" => $request->input("pending"),
        ];

        IncomeCache::put($cachedValues);

        return response()->json([
            "message" => "Se agregó un item",
            "values" => $cachedValues
        ]);

    }

    public function removeItemFromCache($id)
    {
        $cachedValues = collect(IncomeCache::fetchFromCache());

        $filteredValues = $cachedValues->reject(function ($item) use ($id) {
            return $item["id"] == $id;
        });

        IncomeCache::put($filteredValues->values()->toArray());

        return response()->json([
            "message" => "Un item ha sido borrado"
        ]);
    }


    public function cleanCachedIncome()
    {
        IncomeCache::forget();
        RegisterCache::forgetCache();
        return response()->json([
            "message" => "Los registros han sido removidos"
        ]);
    }
}
