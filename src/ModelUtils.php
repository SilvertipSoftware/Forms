<?php

namespace SilvertipSoftware\Forms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait ModelUtils {

    public function convertToModel($obj) {
        if (method_exists($obj, 'toModel')) {
            return call_user_func([$obj, 'toModel']);
        }

        return $obj;
    }

    public function modelNameFrom($obj) {
        if ($obj == null) {
            return null;
        }

        $model = $this->convertToModel($obj);
        return Str::snake(class_basename(get_class($obj)));
    }

    public function paramKeyFor($obj) {
        return $this->modelNameFrom($obj);
    }

    public function isManyRelation($class) {
        $base = class_basename($class);
        return in_array($base, static::$relationsReturningCollections);
    }
}
