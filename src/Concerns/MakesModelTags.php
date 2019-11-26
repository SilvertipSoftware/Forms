<?php

namespace SilvertipSoftware\Forms\Concerns;

use SilvertipSoftware\Forms\Tags;

trait MakesModelTags
{

    public function textFieldWithObject($object, $method, $options = [])
    {
        $tag = new Tags\TextField($object, $method, $this, $options);
        return $tag->render();
    }

    public function numberFieldWithObject($object, $method, $options = [])
    {
        $tag = new Tags\NumberField($object, $method, $this, $options);
        return $tag->render();
    }

    public function passwordFieldWithObject($object, $method, $options = [])
    {
        $tag = new Tags\PasswordField($object, $method, $this, $options);
        return $tag->render();
    }

    public function hiddenFieldWithObject($object, $method, $options = [])
    {
        $tag = new Tags\HiddenField($object, $method, $this, $options);
        return $tag->render();
    }

    public function labelWithObject($object, $method, $text = null, $options = [])
    {
        $tag = new Tags\Label($object, $method, $this, $text, $options);
        return $tag->render();
    }

    public function checkBoxWithObject($object, $method, $options = [], $checkedValue = "1", $uncheckedValue = "0")
    {
        $tag = new Tags\CheckBox($object, $method, $this, $checkedValue, $uncheckedValue, $options);
        return $tag->render();
    }

    public function collectionSelectWithObject(
        $object,
        $method,
        $collection,
        $valueAttrName,
        $textAttrName,
        $options = [],
        $htmlOptions = []
    ) {
        $tag = new Tags\CollectionSelect(
            $object,
            $method,
            $this,
            $collection,
            $valueAttrName,
            $textAttrName,
            $options,
            $htmlOptions
        );
        return $tag->render();
    }
}
