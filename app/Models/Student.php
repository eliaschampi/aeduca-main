<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = "student";

    protected $primaryKey = 'dni';

    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    protected $fillable = ["dni", "branch_code"];

    public function person()
    {
        return $this->belongsTo(Person::class, "dni");
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, "branch_code");
    }

    public function extrainfo()
    {
        return $this->hasOne(ExtraInfo::class, "student_dni", "dni");
    }

    public function registers()
    {
        return $this->hasMany(Register::class, "student_dni", "dni");
    }

    public function families()
    {
        return $this->belongsToMany(Family::class, "family_student", "student_dni", "family_dni")
            ->withPivot("relation_type", "is_main");
    }
}
