<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraInfo extends Model
{
    protected $table = "extrainfo";

    protected $primaryKey = "code";

    public $timestamps = false;

    protected $fillable = [
        "student_dni",
        "type",
        "info",
        "attached"
    ];

}
