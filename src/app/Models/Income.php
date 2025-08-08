<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $table = "income";

    protected $primaryKey = "code";

    protected $guarded = ["code"];

    protected $appends = ["serie"];

    public function getSerieAttribute($value)
    {
        return $this->series . " " . $this->correlative;
    }

    public function getNameAttribute($value)
    {
        $name = "";
        $type = "c";
        if (!empty($this->hasRegister)) {
            $reg = $this->hasRegister->register;
            $person = Person::find($reg->student_dni);
            $name = $person->name . " " . $person->lastname;
            $type = $reg->section_code;
        } elseif (!empty($this->hasCustomer)) {
            if (empty($this->hasCustomer->customer)) {
                $name = $this->hasCustomer->name;
            } else {
                $name = $this->hasCustomer->customer->name;
            }
        }
        return [
            "name" => $name,
            "type" => $type,
        ];
    }

    public function hasRegister()
    {
        return $this->hasOne(RegisterIncome::class, "income_code");
    }

    public function hasCustomer()
    {
        return $this->hasOne(CustomerIncome::class, "income_code");
    }

    public function canceled()
    {
        return $this->hasOne(Canceled::class, "income_code");
    }

    public function cash()
    {
        return $this->belongsTo(Cash::class, "cash_code");
    }
}
