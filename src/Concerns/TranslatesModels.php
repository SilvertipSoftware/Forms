<?php

namespace SilvertipSoftware\Forms\Concerns;

use Illuminate\Support\Str;

trait TranslatesModels {

    public function translate($attr = null, $objectName = null, $tag = null, $options = []) {
        $attr = $attr ?? $this->attr;
        $objectName = $objectName ?? $this->objectName;
        $tag = $tag ?? Str::snake(class_basename($this));
        $searchKeys = [
            Str::plural($objectName) . '.' . $tag . '.' . $attr,
            Str::plural($objectName) . '.' . $attr
        ];

        foreach ($searchKeys as $key) {
            $str = __($key, $options);
            if ($str != $key) {
                return $str;
            }
        }

        return Str::title(str_replace('_', ' ', $attr));
    }
}
