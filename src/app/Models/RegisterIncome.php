<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegisterIncome extends Model
{
    protected $table = "register_income";

    protected $primaryKey = 'income_code';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        "register_code",
        "income_code"
    ];

    public function register()
    {
        return $this->belongsTo(Register::class, "register_code");
    }
}
