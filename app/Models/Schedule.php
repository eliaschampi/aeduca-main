<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = "schedule";

    protected $primaryKey = "code";

    public $timestamps = false;

    protected $guarded = ["code"];

    protected $attributes = [
        "state" => true,
    ];

    public function op()
    {
        return $this->belongsTo(Op::class, "op_code");
    }
}
