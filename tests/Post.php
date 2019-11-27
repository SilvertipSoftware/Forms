<?php

namespace SilvertipSoftware\Forms\Tests;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = [];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }
    
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function aFunc()
    {
        return 321;
    }

    // for tests
    public $_acceptNestedAttributes = false;

    public function isNestedAttribute($name)
    {
        return $this->_acceptNestedAttributes;
    }
}
