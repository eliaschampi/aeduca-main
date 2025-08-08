<?php

namespace App\Models;

use App\Helpers\MainHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Op extends Model
{
    protected $table = "op";

    protected $primaryKey = "code";

    public $timestamps = false;

    protected function sts(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => MainHelper::to_pg_array($value),
            get: fn ($value) => explode(',', trim($value, '{}'))
        );
    }

    protected $fillable = ["course_code", "teacher_dni", "sts"];

    public function person()
    {
        return $this->belongsTo(Person::class, "teacher_dni");
    }

    public function course()
    {
        return $this->belongsTo(Course::class, "course_code");
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, "op_code");
    }
}
