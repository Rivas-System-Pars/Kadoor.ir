<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecType extends Model
{
    protected $guarded = ['id'];

    public function specificationGroups()
    {
        return $this->belongsToMany(SpecificationGroup::class, 'spec_type_specification')->withPivot(['specification_group_id', 'group_ordering', 'specification_ordering'])->withTimestamps()->orderBy('group_ordering');
    }

    public function specifications()
    {
        return $this->belongsToMany(Specification::class)->withPivot(['specification_group_id', 'group_ordering', 'specification_ordering'])->withTimestamps()->orderBy('specification_ordering');
    }

}
