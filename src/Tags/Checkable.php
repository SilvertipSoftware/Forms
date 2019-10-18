<?php

namespace SilvertipSoftware\Forms\Tags;

use Illuminate\Support\Arr;

trait Checkable {

    protected function isInputChecked(&$options) {
        if (array_key_exists('checked', $options)) {
            $checked = Arr::pull($options, 'checked');
            return ($checked === true || $checked === 'checked');
        } else {
            return $this->isChecked($this->value());
        }
    }
}
