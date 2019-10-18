<?php

namespace SilvertipSoftware\Forms\Facades;

use Illuminate\Support\Facades\Facade;
use SilvertipSoftware\Forms\FormHelper;

class Form extends Facade
{

    protected static function getFacadeAccessor()
    {
        return FormHelper::class;
    }
}
