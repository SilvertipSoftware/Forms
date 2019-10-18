<?php

namespace SilvertipSoftware\Forms\Tags;

use SilvertipSoftware\Forms\TagHelper;
use SilvertipSoftware\Forms\Concerns\TranslatesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class CollectionSelect extends Base {

    protected $valueAttrName;
    protected $textAttrName;
    protected $collection;

    public function __construct(
        $objectName,
        $attr,
        $helper,
        $collection,
        $valueAttrName,
        $textAttrName,
        $options = [],
        $htmlOptions = []
    ) {
        parent::__construct($objectName, $attr, $helper, $options);

        $this->collection = $collection;
        $this->valueAttrName = $valueAttrName;
        $this->textAttrName = $textAttrName;
        $this->htmlOptions = $htmlOptions;
    }

    public function render() {
        $optionsForOptions = [
            'selected' => Arr::get($this->options, 'selected', $this->value()),
            'disabled' => Arr::get($this->options, 'disabled', false)
        ];

        return $this->selectContentTag(
            $this->optionsFromCollection(
                $this->collection,
                $this->valueAttrName,
                $this->textAttrName,
                $optionsForOptions
            ),
            $this->options,
            $this->htmlOptions
        );
    }
}
