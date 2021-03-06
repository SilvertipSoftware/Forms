<?php

namespace SilvertipSoftware\Forms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class FormBuilder {
    use ModelUtils;
    use Concerns\TranslatesModels;

    public $objectName;
    public $object;
    public $options;

    protected $multipart;
    protected $index;

    protected $helper;
    protected $nestedChildIndex;
    protected $defaultOptions;
    protected $defaultHtmlOptions;

    public function __construct($objectName, $object, $template, $options) {
        $this->objectName = $objectName;
        $this->object = $object;
        $this->template = $template;
        $this->options = $options;

        $this->nestedChildIndex = [];

        $this->defaultOptions = $options ? Arr::only($options, ['index', 'namespace', 'skip_default_ids']) : [];
        $this->defaultHtmlOptions = Arr::except($this->defaultOptions, ['skip_default_ids']);

        if (preg_match('/\[\]$/', $this->objectName) === 1) {
            $temp = preg_replace('/\[\]$/', '', $this->objectName);
            $object = $object ?? $this->template->getContextVariable($temp);
            if (method_exists($object, 'getRouteKey')) {
                $this->autoIndex = $object->getRouteKey();
            } else {
                throw new \Exception('object[] naming needs a getRouteKey() method on ' . $temp);
            }
        }

        $this->index = $options['index'] ?? $options['child_index'] ?? null;
    }

    public function textField($method, $options = []) {
        return $this->template->textFieldWithObject($this->objectName, $method, $this->objectify($options));
    }

    public function numberField($method, $options = []) {
        return $this->template->numberFieldWithObject($this->objectName, $method, $this->objectify($options));
    }

    public function passwordField($method, $options = []) {
        return $this->template->passwordFieldWithObject($this->objectName, $method, $this->objectify($options));
    }

    public function emailField($method, $options = []) {
        return $this->template->emailFieldWithObject($this->objectName, $method, $this->objectify($options));
    }

    public function hiddenField($method, $options = []) {
        return $this->template->hiddenFieldWithObject($this->objectName, $method, $this->objectify($options));
    }

    public function checkBox($method, $options = [], $checkedValue = "1", $uncheckedValue = "0") {
        return $this->template->checkBoxWithObject(
            $this->objectName,
            $method,
            $this->objectify($options),
            $checkedValue,
            $uncheckedValue
        );
    }

    public function radioButton($method, $value, $options = []) {
        if (is_array($value) && empty($options)) {
            $options = $value;
            $value = 1;
        }

        return $this->template->radioButtonWithObject(
            $this->objectName,
            $method,
            $value,
            $this->objectify($options)
        );
    }

    public function textArea($method, $text = null, $options = []) {
        if (is_array($text)) {
            $options = $text;
            $text = null;
        }
        return $this->template->textAreaWithObject($this->objectName, $method, $text, $this->objectify($options));
    }

    public function submit($value = null, $options = []) {
        if (is_array($value)) {
            $options = $value;
            $value = null;
        }
        $value = $value ?? $this->defaultSubmitValue();
        return $this->template->submitTag($value, $options);
    }

    public function telField($method, $options = []) {
        return $this->template->telFieldWithObject($this->objectName, $method, $this->objectify($options));
    }

    public function dateField($method, $options = []) {
        return $this->template->dateFieldWithObject($this->objectName, $method, $this->objectify($options));
    }

    public function label($method, $text = null, $options = []) {
        if (is_array($text)) {
            $options = $text;
            $text = null;
        }
        return $this->template->labelWithObject($this->objectName, $method, $text, $this->objectify($options));
    }

    public function collectionSelect(
        $method,
        $collection,
        $valueAttrName,
        $textAttrName,
        $options = [],
        $htmlOptions = []
    ) {
        return $this->template->collectionSelectWithObject(
            $this->objectName,
            $method,
            $collection,
            $valueAttrName,
            $textAttrName,
            $this->objectify($options),
            $htmlOptions
        );
    }

    public function formStartTag() {
        return $this->template->formTagWithModel($this->object, $this->options);
    }

    public function end() {
        return $this->template->end();
    }

    public function fieldsFor($recordName, $recordObject = null, $fieldsOptions = []) {
        if (is_array($recordObject)) {
            $fieldsOptions = $recordObject;
            $recordObject = null;
        }
        $fieldsOptions['builder'] = Arr::get($fieldsOptions, 'builder', $this->options['builder'] ?? null);
        $fieldsOptions['namespace'] = Arr::get($fieldsOptions, 'namespace', $this->options['namespace'] ?? null);
        $fieldsOptions['parent_builder'] = $this;

        if (is_string($recordName)) {
            if ($this->isNestedAttributesRelation($recordName)) {
                return $this->fieldsForWithNestedAttributes($recordName, $recordObject, $fieldsOptions);
            }
        } else {
            $recordObject = is_array($recordName) ? end($recordName) : $recordName;
            $recordName = $this->paramKeyFor($recordObject);
        }

        $objectName = $this->objectName;
        $index = Arr::get($this->options, 'index');
        if (!empty($this->autoIndex)) {
            $objectName = preg_replace('/\[\]$/', '', $objectName);
            $index = $this->autoIndex;
        }

        if ($index !== null) {
            $recordName = $objectName . '[' . $index . '][' . $recordName . ']';
        } elseif (preg_match('/\[\]$/', $recordName)) {
            $recordName = $objectName
                . preg_replace('/(.*)\[\]$/', '[\\1][' . $recordObject->getKey() . ']', $recordName);
        } else {
            $recordName = $objectName . '[' . $recordName . ']';
        }
        $fieldsOptions['child_index'] = $index;

        return $this->template->fieldsForWithObject($recordName, $recordObject, $fieldsOptions);
    }

    protected function fieldsForWithNestedAttributes($assocName, $assoc, $options) {
        $name = $this->objectName . '[' . $assocName . '_attributes]';
        $assoc = $this->convertToModel($assoc);

        if ($assoc instanceof Model) {
            $relationClass = get_class($this->object->{$assocName}());
            if ($this->template->isManyRelation($relationClass)) {
                $assoc = [$assoc];
            }
        } elseif (!(is_array($assoc) || $assoc instanceof Collection)) {
            $assoc = $this->object->{$assocName};
        }

        if ($assoc instanceof Collection) {
            $assoc = $assoc->all();
        }

        if (is_array($assoc)) {
            $explicitChildIndex = Arr::get($options, 'child_index');
            $buffer = '';
            foreach ($assoc as $index => $relatedModel) {
                if (is_callable($explicitChildIndex)) {
                    $options['child_index'] = $explicitChildIndex->call($relatedModel, $index);
                } elseif ($explicitChildIndex) {
                    $options['child_index'] = $explicitChildIndex;
                } else {
                    $options['child_index'] = $index;
                }
                $buffer .= $this->fieldsForNestedModel(
                    $name . '[' . $options['child_index'] . ']',
                    $relatedModel,
                    $options
                );
                $buffer;
            }
            return new HtmlString($buffer);
        } else {
            return $this->fieldsForNestedModel($name, $assoc, $options);
        }
    }

    protected function fieldsForNestedModel($name, $model, &$fieldsOptions) {
        $emitHiddenId = $model->exists
            && Arr::get($fieldsOptions, 'include_id', Arr::get($this->options, 'include_id', true));
        $fieldsOptions['include_id'] = $emitHiddenId;

        $content = $this->template->fieldsForWithObject($name, $model, $fieldsOptions);
        return $content;
    }

    protected function defaultSubmitValue()
    {
        $model = $this->convertToModel($this->object);
        $keyPart = $model ? ($model->exists ? 'update' : 'create') : 'submit';
        return $this->translate($keyPart, null, 'submit');
    }

    protected function objectify(&$options) {
        $options['object'] = $this->object;
        $result = array_merge($this->defaultOptions, $options);
        return $result;
    }

    protected function addDefaultNameAndIdFor($value, &$options) {
        if ($value == null) {
            $this->addDefaultNameAndId($options);
        } else {
            $id = $options['id'] ?? null;
            $this->addDefaultNameAndId($options);
            if (empty($id) && array_key_exists('id', $options)) {
                $options['id'] .= '_' . $this->sanitizedValue($value);
            }
        }
    }

    protected function addDefaultNameAndId(&$options) {
        $index = $this->nameAndIdIndex($options);
        $options['name'] = $options['name'] ?? $this->tagName(Arr::get($options, 'multiple'), $index);

        $options['id'] = $options['id'] ?? $this->tagId($index);
        if ($namespace = Arr::pull($options, 'namespace')) {
            $options['id'] = $options['id'] ? $namespace.'_'.$options['id'] : $namespace;
        }
    }

    protected function tagName($multiple = false, $index = null) {
        if (empty($this->objectName)) {
            return $this->helper . ($multiple ? '[]' : '');
        } elseif ($index != null) {
            return $this->objectName . '[' . $index . ']' . $this->helper . ($multiple ? '[]' : '');
        } else {
            return $this->objectName . '[' . $this->helper . ']' . ($multiple ? '[]' : '');
        }
    }

    protected function tagId($index) {
        if (empty($this->objectName)) {
            return $this->helper;
        } elseif ($index != null) {
            return $this->objectName . '_' . $index . '_' . $this->helper;
        } else {
            return $this->objectName . '_' . $this->helper;
        }
    }

    protected function nameAndIdIndex(&$options) {
        return Arr::pull($options, 'index') ?? '';
    }
}
