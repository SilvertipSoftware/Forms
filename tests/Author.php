<?php

namespace SilvertipSoftware\Forms\Tests;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $guarded = [];

    public function getTitledName()
    {
        return 'Dr. ' . $this->name;
    }
}
