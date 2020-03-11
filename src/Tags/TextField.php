<?php

namespace SilvertipSoftware\Forms\Tags;

use Illuminate\Support\Arr;

class TextField extends Base {

    public function render() {
        $options = $this->options;
        if (!array_key_exists('size', $options)) {
            $options['size'] = Arr::get($options, 'maxlength');
        }
        $this->addDefaultNameAndId($options);
        $this->addValueFromFlash($options);
        
        $options['type'] = Arr::get($options, 'type', $this->fieldType());
        if ($options['type'] != 'file') {
            $options['value'] = Arr::get($options, 'value', $this->valueBeforeTypeCast());
        }

        return $this->helper->tag('input', $options);
    }

    protected function fieldType() {
        $type = str_replace('Field', '', class_basename(get_class($this)));
        return strtolower($type);
    }
}
