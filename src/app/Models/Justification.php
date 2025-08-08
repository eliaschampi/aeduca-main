<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Justification extends Model
{
    protected $table = "justification";

    public const UPDATED_AT = null;

    protected $primaryKey = "attendance_code";

    protected $fillable = [
        "attendance_code",
        "attached",
        "description",
        "created_at",
    ];


    public function setUpdatedAt($value)
    {
        // Do nothing.
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class, "attendance_code");
    }
}
