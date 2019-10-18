<?php

namespace SilvertipSoftware\Forms\Tags;

use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;

class Label extends Base {

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
        $options['for'] = Arr::pull($options, 'id');
        Arr::pull($options, 'name');

        $text = $this->text ?? $this->translate();
        return $this->helper->contentTag('label', $text, $options);
    }
}
