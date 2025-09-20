<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Canceled extends Model
{
    protected $table = "canceled";

    protected $primaryKey = 'income_code';

    public $timestamps = false;

    protected $guarded = ["income_code"];
}
