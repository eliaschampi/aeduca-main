<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = "profile";

    protected $primaryKey = 'person_dni';

    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    public function person()
    {
        return $this->belongsTo(Person::class, "person_dni");
    }
}
