<?php

namespace SilvertipSoftware\Forms\Tests;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = [];

    public function author() {
        return $this->belongsTo(Author::class);
    }
    
    public function aFunc() {
        return 321;
    }
}
