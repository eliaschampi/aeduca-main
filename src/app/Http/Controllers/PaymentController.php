<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Repositories\PaymentRepository;

class PaymentController extends Controller
{
    private PaymentRepository $instance;

    public function __construct(PaymentRepository $instance)
    {
        $this->instance = $instance;
    }

    public function fetchByRegister(string $register_code)
    {
        return response()->json([
            "values" => $this->instance->fetchByRegister($register_code)
        ]);
    }

    public function store(PaymentRequest $request)
    {
        $message = "Correctamente guardado";
        $value = $this->instance->store($request->all());
        if (gettype($value) === "string") {
            $message = $value;
            $value = null;
        }
        return response()->json([
            "message" => $message,
            "value" => $value
        ]);
    }

    public function update(PaymentRequest $request, int $code)
    {
        $this->instance->update($request->all(), $code);
        return response()->json([
            "message" => "Correctamente modificado"
        ]);
    }

    public function destroy(int $code)
    {
        $this->instance->destroy($code);
        return response()->json([
            "message" => "Correctamente eliminado"
        ]);
    }
}
