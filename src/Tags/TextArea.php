<?php

namespace SilvertipSoftware\Forms\Tags;

class TextArea extends Base {

    protected $text;

    public function __construct($objectName, $attr, $helper, $textOrOptions, $options) {
        if (is_array($textOrOptions)) {
            $options = $textOrOptions;
            $text = null;
        } else {
            $text = $textOrOptions;
        }

        $this->text = $text;
        parent::__construct($objectName, $attr, $helper, $options);
    }

    public function render() {
        $options = $this->options;
        $this->addDefaultNameAndId($options);
        $this->addValueFromFlash($options);
        $text = $this->text ?? $this->valueBeforeTypeCast();
        return $this->helper->contentTag('textarea', $text, $options);
    }

    protected function addValueFromFlash(&$options) {
        $oldInput = $this->valueFromFlash($options);

        if ($oldInput !== null) {
            $this->text = $oldInput;
        }
    }
}
