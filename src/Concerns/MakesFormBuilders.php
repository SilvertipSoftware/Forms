<?php

namespace SilvertipSoftware\Forms\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use SilvertipSoftware\RestRouter\RestRouter;

trait MakesFormBuilders {

    public function with($model, $options = []) {
        $builder = $this->builderForModelForm($model, $options);
        $this->builders[] = $builder;

        return $builder->formStartTag();
    }

    public function fieldsForWithObject($name, $object, $options = []) {
        $builder = $this->instantiateBuilder($name, $object, $options);
        $this->builders[] = $builder;
        $ret = '';

        if ($view = Arr::get($options, 'view')) {
            $variableName = Arr::get($options, 'as', $this->modelNameFrom($object) ?? 'object');
            $data = [
                $variableName => $object
            ];
            $ret = \View::make($view, array_merge($data, $options))->render();
            if (Arr::get($options, 'include_id')) {
                $ret .= $builder->hiddenField('id');
            }
        }
        $this->end();
        return new HtmlString($ret);
    }

    public function currentBuilder() {
        return end($this->builders) ?? null;
    }

    public function end() {
        $ret = '';
        if (count($this->builders) == 1) {
            $ret = $this->closeTag('form');
        }
        array_pop($this->builders);
        return $ret;
    }

    protected function builderForModelForm(&$model, &$options = []) {
        if (is_array($model) && Arr::isAssoc($model)) {
            $options = $model;
            $model = Arr::pull($options, 'model');
        }

        $scope = Arr::pull($options, 'scope');
        $url = Arr::pull($options, 'url');
        $format = Arr::pull($options, 'format');

        $options['skip_default_ids'] = !static::$formWithGeneratesIds;

        if ($model) {
            $url = $url ?? RestRouter::path($model, ['format' => $format]);
            $model = is_array($model) ? end($model) : $model;
            $scope = $scope ?? $this->modelNameFrom($model);
        }
        $options['url'] = $url;

        $builder = $this->instantiateBuilder($scope, $model, $options);
        return $builder;
    }

    protected function instantiateBuilder($name, $object, $options) {
        if (!is_string($name)) {
            $object = $name;
            $name = $object ? $this->modelNameFrom($object) : $name;
        }
        $builderClass = $options['builder'] ?? static::$defaultFormBuilderClass;

        return new $builderClass($name, $object, $this, $options);
    }
}
