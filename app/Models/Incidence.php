<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incidence extends Model
{
    protected $table = "incidence";

    protected $primaryKey = "code";

    protected $fillable = [
        "user_code",
        "type",
        "title",
        "description",
        "agreement",
        "created_at",
        "file_attached",
        "state",
        "extra",
        "branch_code",
    ];

    protected $attributes = [
        "state" => true,
    ];

    public function user()
    {
        return $this->belongsTo(User::class, "user_code");
    }

    public function persons()
    {
        return $this->belongsToMany(Person::class, "incidence_entity", "incidence_code", "entity_identifier")
            ->withPivot("entity_type", "actor_type");
    }
}
