<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerIncome extends Model
{
    protected $table = "customer_income";

    protected $primaryKey = 'income_code';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        "customer_code",
        "income_code",
        "name",
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, "customer_code");
    }
}
