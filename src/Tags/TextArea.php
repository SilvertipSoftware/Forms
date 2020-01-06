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

        $text = $this->text ?? '';
        return $this->helper->contentTag('textarea', $text, $options);
    }
}