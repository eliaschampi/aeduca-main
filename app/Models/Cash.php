<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cash extends Model
{
    protected $table = "cash";

    protected $primaryKey = 'code';

    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    protected $fillable = [
        "code",
        "branch_code",
        "cash",
        "state",
        "user_code"
    ];

    public function surrendered()
    {
        return $this->hasOne(Surrendered::class, "cash_code");
    }

    public function user()
    {
        return $this->hasOne(User::class, "code", "user_code");
    }

    public function incomes()
    {
        return $this->hasMany(Income::class, "cash_code")->where("state", true);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, "cash_code");
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, "branch_code");
    }
}
