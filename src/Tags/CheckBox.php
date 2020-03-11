<?php

namespace SilvertipSoftware\Forms\Tags;

use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;

class CheckBox extends Base {
    use Checkable;

    public function __construct($objectName, $attr, $helper, $checkedValue, $uncheckedValue, $options) {
        $this->checkedValue = $checkedValue;
        $this->uncheckedValue = $uncheckedValue;
        parent::__construct($objectName, $attr, $helper, $options);
    }

    public function render() {
        $options = $this->options;
        $options['type'] = 'checkbox';
        $options['value'] = $this->checkedValue;

        $this->addValueFromFlash($options);
        if ($this->isInputChecked($options)) {
            $options['checked'] = 'checked';
        }

        if (Arr::get($options, 'multiple')) {
            $this->addDefaultNameAndIdForValue($this->checkedValue, $options);
            Arr::pull($options, 'multiple');
        } else {
            $this->addDefaultNameAndId($options);
        }

        $includeHidden = Arr::pull($options, 'include_hidden', true);

        $checkbox = $this->tag('input', $options);
        if ($includeHidden) {
            $hidden = $this->hiddenFieldForCheckBox($options);
            $tags = $hidden . $checkbox;
        } else {
            $tags = '' . $checkbox;
        }

        return new HtmlString($tags);
    }

    protected function isChecked($value) {
        if ($value === true || $value === false) {
            return ($value == !!$this->checkedValue);
        } elseif ($value == null) {
            return false;
        } elseif (is_string($value)) {
            return $value == $this->checkedValue;
        } elseif (is_array($value)) {
            return in_array($this->checkedValue, $value);
        } else {
            return intval($value) == intval($this->checkedValue);
        }
    }

    protected function hiddenFieldForCheckBox($options) {
        if ($this->uncheckedValue != null) {
            return $this->tag('input', array_merge(
                Arr::only($options, ['name', 'disabled', 'form']),
                [ 'type' => 'hidden', 'value' => $this->uncheckedValue]
            ));
        } else {
            return '';
        }
    }

    protected function addValueFromFlash(&$options) {
        $oldInput = $this->valueFromFlash($options);

        if ($oldInput !== null) {
            $options['checked'] = $this->isChecked($oldInput);
        }
    }
}
