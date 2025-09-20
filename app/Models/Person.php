<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $table = "person";

    protected $primaryKey = 'dni';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        "dni",
        "name",
        "lastname",
        "birthdate",
        "gender",
        "ubigeo",
        "district",
        "address",
        "email",
        "phone",
        "obs"
    ];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucwords(strtolower($value));
    }

    public function setLastnameAttribute($value)
    {
        $this->attributes['lastname'] = ucwords(strtolower($value));
    }

    public function profile()
    {
        return $this->hasOne(Profile::class, "person_dni", "dni");
    }

    public function student()
    {
        return $this->hasOne(Student::class, "dni", "dni");
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class, "dni", "dni");
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, "entity_identifier");
    }
}
