<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttributeGroup extends Model
{
    use Filterable;

    protected $guarded = ['id'];

    public function get_attributes()
    {
        return $this->hasMany(Attribute::class);
    }
}
