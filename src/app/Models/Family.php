<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    protected $table = "family";

    protected $primaryKey = 'dni';

    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    protected $fillable = ["dni", "telephone", "institute", "degree", "profession"];

    public function person()
    {
        return $this->belongsTo(Person::class, "dni");
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, "family_student", "family_dni", "student_dni")
            ->withPivot("relation_type", "is_main");
    }
}
