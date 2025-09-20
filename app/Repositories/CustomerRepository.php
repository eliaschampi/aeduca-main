<?php

namespace App\Repositories;

use App\Models\Customer;

class CustomerRepository
{
    public function fetchAll()
    {
        return Customer::all();
    }

    public function store(array $data): Customer
    {
        return Customer::create($data);
    }

    public function update(array $data, int $code)
    {
        $customer = Customer::find($code);
        $customer->name = $data["name"];
        $customer->contact_number = $data["contact_number"];
        return $customer->save();
    }

    public function destroy($code)
    {
        return Customer::destroy($code);
    }
}
