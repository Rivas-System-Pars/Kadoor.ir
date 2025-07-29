<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecificationGroup extends Model
{
    protected $guarded = ['id'];

    public function specifications()
    {
        return $this->belongsToMany(Specification::class);
    }
}
