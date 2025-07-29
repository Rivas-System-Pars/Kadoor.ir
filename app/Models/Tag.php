<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Tag extends Model
{
    use sluggable;

    protected $guarded = ['id'];

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function sluggable() : array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }
}
