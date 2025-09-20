<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = "branch";

    protected $primaryKey = 'code';

    public $timestamps = false;

    protected $guarded = ["code"];
}
