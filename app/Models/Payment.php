<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = "payment";

    protected $primaryKey = "code";

    public $timestamps = false;

    protected $fillable = [
        "register_code",
        "pcode",
        "description",
        "topay",
        "paid",
        "amount",
        "payment_type"
    ];
}
