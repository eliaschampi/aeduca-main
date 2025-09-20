<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Degree extends Model
{
    protected $table = "degree";

    protected $primaryKey = 'code';

    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    protected $fillable = ["code", "cycle_code"];

    protected $appends = ['full_name'];

    public function cycle()
    {
        return $this->belongsTo(Cycle::class, "cycle_code");
    }

    public function sections()
    {
        return $this->hasMany(Section::class, "degree_code");
    }

    public function getFullNameAttribute()
    {
        $level = [
            "1" => "1ro",
            "2" => "2do",
            "3" => "3ro",
            "4" => "4to",
            "5" => "5to",
            "6" => "6to",
        ];
        return $level[substr($this->code, -1)];
    }
}
