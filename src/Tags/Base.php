<?php

namespace SilvertipSoftware\Forms\Tags;

use SilvertipSoftware\Forms\TagHelper;
use SilvertipSoftware\Forms\Concerns\TranslatesModels;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class Base {
    use TagHelper,
        TranslatesModels;

    protected $objectName;
    protected $attr;
    protected $options;
    protected $helper;
    protected $object;

    protected $cachedSanitizedObjectName;
    protected $generateIndexedNames;
    protected $autoIndex;

    public function __construct($objectName, $attr, $helper, $options = []) {
        $this->attr = $attr;
        $this->helper = $helper;

        $temp = preg_replace('/\[\]$/', '', $objectName);
        if ($temp == $objectName) {
            $this->objectName = preg_replace(('/\[\]\]$/'), ']', $temp);
            if ($this->objectName != $temp) {
                $indexable = substr($this->objectName, 0, -1);
            }
        } else {
            $indexable = $temp;
        }

        $this->object = Arr::pull($options, 'object');

        $this->skipDefaultIds = Arr::pull($options, 'skip_default_ids');
        $this->options = $options;

        if ($this->objectName != $objectName) {
            $this->generateIndexedNames = true;
            $this->autoIndex = $this->getAutoIndex($indexable);
        } else {
            $this->generateIndexedNames = false;
            $this->autoIndex = null;
        }
    }

    protected function result($object, $attr) {
        if (method_exists($object, $attr)) {
            return call_user_func([$object, $attr]);
        } elseif (is_object($object)) {
            return $object->{$attr};
        } else {
            return Arr::get($object, $attr);
        }
    }

    protected function value() {
        return $this->result($this->object, $this->attr);
    }

    protected function valueBeforeTypeCast() {
        return $this->value();
    }

    protected function didValueComeFromUser() {
        return true;
    }

    protected function getAutoIndex($str) {
        $object = $this->object ?? $this->helper->getContextVariable($str);
        if ($object && method_exists($object, 'getRouteKey')) {
            return $object->getRouteKey();
        } else {
            throw new Exception('object[] naming needs a getRouteKey() method on ' . $str);
        }
    }

    protected function addDefaultNameAndIdForValue($tagValue, &$options) {
        if ($tagValue == null) {
            $this->addDefaultNameAndId($options);
        } else {
            $specifiedId = Arr::get($options, 'id');
            $this->addDefaultNameAndId($options);
            if (empty($specifiedId) && array_key_exists('id', $options)) {
                $options['id'] .= '_' . $this->sanitizedValue($tagValue);
            }
        }
    }

    protected function addDefaultNameAndId(&$options) {
        $index = $this->nameAndIdIndex($options);
        $options['name'] = Arr::get($options, 'name', $this->tagName(Arr::get($options, 'multiple'), $index));

        if (!$this->skipDefaultIds) {
            $options['id'] = Arr::get($options, 'id', $this->tagId($index));

            $namespace = Arr::pull($options, 'namespace');
            if ($namespace) {
                $options['id'] = $options['id'] ? $namespace . '_' . $options['id'] : $namespace;
            }
        }
    }

    protected function tagName($multiple = false, $index = null) {
        if (empty($this->objectName)) {
            return $this->sanitizedMethodName() . ($multiple ? '[]' : '');
        } elseif ($index) {
            return $this->objectName
                . '[' . $index . '][' . $this->sanitizedMethodName() . ']'
                . ($multiple ? '[]' : '');
        } else {
            return $this->objectName . '[' . $this->sanitizedMethodName() . ']' . ($multiple ? '[]' : '');
        }
    }

    protected function tagId($index = null) {
        if (empty($this->objectName)) {
            return $this->sanitizedMethodName();
        } elseif ($index) {
            return $this->sanitizedObjectName() . '_' . $index . '_' . $this->sanitizedMethodName();
        } else {
            return $this->sanitizedObjectName() . '_' . $this->sanitizedMethodName();
        }
    }

    protected function sanitizedObjectName() {
        if (!$this->cachedSanitizedObjectName) {
            $temp = preg_replace('/\]\[|[^-a-zA-Z0-9:.]/', '_', $this->objectName);
            $this->cachedSanitizedObjectName = preg_replace('/_$/', '', $temp);
        }
        return $this->cachedSanitizedObjectName;
    }

    protected function sanitizedMethodName() {
        return $this->attr;
    }

    protected function sanitizedValue($value) {
        $temp = preg_replace('/\s/', '_', $value);
        $temp = preg_replace('/[^-[[:word:]]]/', '', $temp);
        return strtolower($temp);
    }

    protected function nameAndIdIndex(&$options) {
        if (array_key_exists('index', $options)) {
            return Arr::pull($options, 'index', '');
        } elseif ($this->generateIndexedNames) {
            return $this->autoIndex ?? '';
        }
    }

    protected function selectContentTag($optionTags, $options, $htmlOptions) {
        $this->addDefaultNameAndId($htmlOptions);

        if ($this->isPlaceholderRequired($htmlOptions)) {
            if (!Arr::get($options, 'include_blank', false)) {
                throw new Exception("Required selects must have include_blank set");
            }
            $options['include_blank'] = $options['include_blank'] ?? (Arr::get($options, 'prompt') ? false : true);
        }

        $value = Arr::get($options, 'selected', $this->value());
        $tags = $this->contentTag('select', $this->addOptions($optionTags, $options, $value), $htmlOptions);

        if (Arr::get($htmlOptions, 'multiple') && Arr::get($options, 'include_hidden', true)) {
            $tags = $this->hiddenFieldForSelect($options) . $tags;
        }

        return new HtmlString(''.$tags);
    }


    protected function optionsFromCollection($collection, $valueAttrName, $textAttrName, $seldis) {
        if ($collection instanceof Collection) {
            $collection = $collection->all();
        }

        $optionArray = array_map(function ($item) use ($valueAttrName, $textAttrName) {
            return [$this->result($item, $textAttrName), $this->result($item, $valueAttrName), []];
        }, $collection);

        return $this->optionsForSelect($optionArray, $seldis);
    }

    protected function optionsForSelect($container, $seldis) {
        if (is_string($container)) {
            return $container;
        }

        $optionStrings = array_map(function ($item) use ($seldis) {
            $text = $item[0];
            $value = $item[1];
            $htmlOptions = [];
            $selected = Arr::get($seldis, 'selected', []);
            $disabled = Arr::get($seldis, 'disabled', []);

            if ($this->isValueSelected($value, $selected)) {
                $htmlOptions['selected'] = true;
            }
            if ($disabled && $this->isValueSelected($value, $disabled)) {
                $htmlOptions['disabled'] = true;
            }
            $htmlOptions['value'] = $value;
            return $this->contentTag('option', $text, $htmlOptions);
        }, $container);

        return new HtmlString(implode("\n", $optionStrings));
    }

    protected function isValueSelected($value, $valOrArray) {
        $array = (array)$valOrArray;
        return in_array($value, $array);
    }

    protected function isPlaceholderRequired($options) {
        # See https://html.spec.whatwg.org/multipage/forms.html#attr-select-required
        return Arr::get($options, 'required') && !Arr::get($options, 'multiple') && Arr::get($options, 'size', 1) == 1;
    }

    protected function addOptions($optionTags, $options, $value = null) {
        $blank = Arr::get($options, 'include_blank');
        if ($blank) {
            $optionTags = $this->contentTag('option', is_string($blank)
                ? $blank
                : '', ['value' => '']) . "\n" . $optionTags;
        }

        $prompt = Arr::get($options, 'prompt');
        if (empty($value) && $prompt) {
            $tagOptions = ['value' => ''];
            if (Arr::get($options, 'disabled')) {
                $tagOptions['disabled'] = true;
            }
            if (Arr::get($options, 'selected')) {
                $tagOptions['selected'] = true;
            }
            $optionTags = $this->contentTag('option', $this->promptText($prompt), $tagOptions) . "\n" . $optionTags;
        }

        return new HtmlString(''.$optionTags);
    }

    protected function promptText($prompt) {
        return new HtmlString(__($prompt));
    }

    protected function hiddenFieldForSelect($options) {
        return $this->tag('input', array_merge(
            Arr::only($options, ['name', 'disabled', 'form']),
            [ 'type' => 'hidden', 'value' => '']
        ));
    }
}
