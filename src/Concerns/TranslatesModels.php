<?php

namespace SilvertipSoftware\Forms\Concerns;

use Illuminate\Support\Str;

trait TranslatesModels {

    public function translate($attr = null, $objectName = null, $tag = null, $options = []) {
        $attr = $attr ?? $this->attr;
        $prefix = $this->objectLangPrefix($objectName ?? $this->objectName);
        $tag = $tag ?? Str::snake(class_basename($this));
        $searchKeys = [
            $prefix . '.' . $tag . '.' . $attr,
            $prefix . '.' . $attr
        ];

        foreach ($searchKeys as $key) {
            $str = __($key, $options);
            if ($str != $key) {
                return $str;
            }
        }

        return Str::title(str_replace('_', ' ', $attr));
    }

    protected function objectLangPrefix($name) {
        $temp = preg_replace('/\]\[|[^-a-zA-Z0-9_\-:.]/', '.', $name);
        $temp = preg_replace('/\.$/', '', $temp);
        $parts = explode('.', $temp);
        if (count($parts) > 0) {
            $parts[0] = Str::plural($parts[0]);
        }

        return implode('.', $parts);
    }
}
