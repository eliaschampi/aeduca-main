<?php

namespace App\Repositories;

use App\Models\Payment;

class PaymentRepository
{
    public function fetchByRegister(string $register_code)
    {
        return Payment::where("register_code", $register_code)
            ->orderBy("topay", "asc")->get();
    }

    public function store(array $payment)
    {
        $check = Payment::where("register_code", $payment["register_code"])
            ->where("pcode", $payment["pcode"])
            ->where("description", $payment["description"])
            ->first();
        if (!empty($check)) {
            return "El pago ya esta registrado";
        }
        return Payment::create($payment);
    }

    public function update(array $payment, int $code)
    {
        $instance = Payment::find($code);
        $instance->pcode = $payment["pcode"];
        $instance->description = $payment["description"];
        $instance->topay = $payment["topay"];
        $instance->paid = $payment["paid"];
        $instance->amount = $payment["amount"];
        $instance->payment_type = $payment["payment_type"];
        return $instance->save();
    }

    public function destroy(int $code)
    {
        return Payment::destroy($code);
    }

    public static function togglePaid(int $pcode, string $register_code, string|null $month)
    {
        if ($pcode !== 200) {
            return Payment::where("pcode", $pcode)
                ->where("register_code", $register_code)
                ->update(["paid" => date("Y-m-d")]);
        }

        return Payment::where("pcode", $pcode)
            ->where("register_code", $register_code)
            ->where("description", $month)->update(["paid" => date("Y-m-d")]);
    }
}
