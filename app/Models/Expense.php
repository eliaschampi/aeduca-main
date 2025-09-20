<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = "expense";

    protected $primaryKey = "code";

    protected $fillable = [
        "cashactiontype_code",
        "description",
        "voucher_num",
        "cash_code",
        "total"
    ];

    public function cash()
    {
        return $this->belongsTo(Cash::class, "cash_code");
    }

    public function actiontype()
    {
        return $this->belongsTo(CashActionType::class, "cashactiontype_code");
    }

    public function detail()
    {
        return $this->hasMany(ExpenseDetail::class, "expense_code");
    }
}
