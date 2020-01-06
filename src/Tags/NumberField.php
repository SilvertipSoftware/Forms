<?php

namespace SilvertipSoftware\Forms\Tags;

use Illuminate\Support\Arr;

class NumberField extends TextField {

    public function render() {
        $range = Arr::get($this->options, 'in');
        if ($range && is_array($range) && count($range) == 2) {
            $this->options['min'] = $range[0];
            $this->options['max'] = $range[1];
        }
        return parent::render();
    }
}
