<?php

namespace App\Http\Controllers;

use App\Repositories\CustomerRepository;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private CustomerRepository $instance;

    public function __construct(CustomerRepository $instance)
    {
        $this->instance = $instance;
    }

    public function index()
    {
        return response()->json([
            "values" => $this->instance->fetchAll(),
        ]);
    }

    public function store(Request $request)
    {
        $customer = $this->instance->store($request->all());
        return response()->json([
            "message" => "correctamente guardado",
            "customer" => $customer,
        ]);
    }

    public function update(Request $request, $code)
    {
        $this->instance->update($request->all(), $code);
        return response()->json([
            "message" => "correctamente actualizado",
        ]);
    }

    public function destroy($code)
    {
        $this->instance->destroy($code);
        return response()->json([
            "message" => "correctamente eliminado",
        ]);
    }
}
