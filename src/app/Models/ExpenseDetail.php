<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseDetail extends Model
{
    protected $table = "expense_detail";

    protected $primaryKey = 'code';

    public $timestamps = false;

    protected $guarded = ["code"];
}
