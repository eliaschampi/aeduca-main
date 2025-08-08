<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $table = "teacher";

    protected $primaryKey = "dni";

    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = "string";

    protected $fillable = ["dni", "specialty", "startdate", "branch_code"];

    public function person()
    {
        return $this->belongsTo(Person::class, "dni");
    }

    public function ops()
    {
        return $this->hasMany(Op::class, "teacher_dni");
    }
}
