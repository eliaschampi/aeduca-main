<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeDetail extends Model
{
    protected $table = "income_detail";

    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = "code";

    protected $fillable = [
        "income_code",
        "cashactiontype_code",
        "title",
        "topay",
        "discount",
        "paid",
    ];

    public function income()
    {
        return $this->belongsTo(Income::class, "income_code")->where("state", true);
    }

    public function actiontype()
    {
        return $this->belongsTo(CashActionType::class, "cashactiontype_code");
    }
}
