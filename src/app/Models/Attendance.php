<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = "attendance";

    protected $primaryKey = 'code';

    protected $fillable = [
        "code",
        "entry_time",
        "state",
        "entity_identifier",
        "entity_type",
        "branch_code",
        "priority",
    ];

    public function person()
    {
        return $this->belongsTo(Person::class, "entity_identifier");
    }

    public function justification()
    {
        return $this->hasOne(Justification::class, "attendance_code");
    }
}
