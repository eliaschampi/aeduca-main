<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignHistory extends Model
{
    protected $table = "sign_history";

    protected $primaryKey = "code";

    public $timestamps = false;

    protected $fillable = ["user_code", "device", "token", "created_at"];
}
