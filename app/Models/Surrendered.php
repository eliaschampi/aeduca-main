<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Surrendered extends Model
{
    protected $table = "surrendered";

    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = "cash_code";

    protected $keyType = 'string';

    protected $fillable = [
        "cash_code",
        "amount",
        "recipient",
        "reason"
    ];

    public function cash()
    {
        return $this->belongsTo(Cash::class, "cash_code");
    }
}
