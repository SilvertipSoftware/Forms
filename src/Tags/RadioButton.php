<?php

namespace SilvertipSoftware\Forms\Tags;

use Illuminate\Support\HtmlString;

class RadioButton extends Base {
    use Checkable;

    public function __construct($objectName, $attr, $helper, $tagValue, $options) {
        $this->tagValue = $tagValue;
        parent::__construct($objectName, $attr, $helper, $options);
    }

    public function render() {
        $options = $this->options;
        $options['type'] = 'radio';
        $options['value'] = $this->tagValue;

        $this->addValueFromFlash($options);
        if ($this->isInputChecked($options)) {
            $options['checked'] = 'checked';
        }

        $this->addDefaultNameAndId($options);

        return $this->tag('input', $options);
    }

    public function isChecked($value) {
        return $value === $this->tagValue;
    }
}
