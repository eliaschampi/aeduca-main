<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $table = "section";

    protected $primaryKey = 'code';

    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    protected $fillable = ["code", "name", "degree_code", "tutor"];

    public function degree()
    {
        return $this->belongsTo(Degree::class, "degree_code");
    }

    public function registers()
    {
        return $this->hasMany(Register::class, "section_code");
    }
}
