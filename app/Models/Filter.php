<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Filter extends Model
{
    protected $guarded = ['id'];

    public function related()
    {
        return $this->hasMany(Filterable::class);
    }

    public function filterable()
    {
        return $this->morphTo();
    }
}
