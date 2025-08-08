<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    protected $table = "tracking";

    protected $primaryKey = "code";
    public const UPDATED_AT = null;
    protected $fillable = ["user_code", "branch_code", "action", "description"];

    public function user()
    {
        return $this->belongsTo(User::class, "user_code");
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, "branch_code");
    }
}
