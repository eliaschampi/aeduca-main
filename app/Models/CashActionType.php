<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashActionType extends Model
{
    protected $table = "cashactiontype";

    protected $primaryKey = 'code';

    public $timestamps = false;

    protected $guarded = ["code"];
}
