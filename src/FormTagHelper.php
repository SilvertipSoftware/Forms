<?php

namespace SilvertipSoftware\Forms;

use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

trait FormTagHelper {

    public function formTagWithModel($model = null, $options = []) {
        $options['method'] = $options['method'] ?? (($model && $model->exists) ? 'patch' : 'post');
        $url = Arr::pull($options, 'url');
        return $this->formTag($url, $options);
    }

    public function formTag($urlForOptions = [], $options = []) {
        $htmlOptions = $this->htmlOptionsForForm($urlForOptions, $options);
        return $this->formTagHtml($htmlOptions);
    }

    public function formTagHtml($options) {
        $extraTags = $this->extraTagsForForm($options);
        return new HtmlString($this->tag('form', $options, true) . $extraTags);
    }

    public function textFieldTag($name, $value = null, $options = []) {
        return $this->tag('input', array_merge([
            'type' => 'text',
            'name' => $name,
            'id' => $this->sanitizeToId($name),
            'value' => $value
        ], $options));
    }

    public function numberFieldTag($name, $value = null, $options = []) {
        return $this->tag('input', array_merge([
            'type' => 'number',
            'name' => $name,
            'id' => $this->sanitizeToId($name),
            'value' => $value
        ], $options));
    }

    public function passwordFieldTag($name, $value = null, $options = []) {
        return $this->tag('input', array_merge([
            'type' => 'password',
            'name' => $name,
            'id' => $this->sanitizeToId($name),
            'value' => $value
        ], $options));
    }

    public function hiddenFieldTag($name, $value = null, $options = []) {
        return $this->tag('input', array_merge([
            'type' => 'hidden',
            'name' => $name,
            'id' => $this->sanitizeToId($name),
            'value' => $value
        ], $options));
    }

    public function labelTag($name = null, $content = null, $options = []) {
        if (!empty($name) && !array_key_exists('for', $options)) {
            $options['for'] = $this->sanitizeToId($name);
        }

        return $this->contentTag('label', $content ?? Str::title(Str::slug($name)), $options);
    }

    public function submitTag($value = null, $options = []) {
        $tagOptions = array_merge(
            [
                'type' => 'submit',
                'name' => 'commit',
                'value' => $value
            ],
            $options
        );
        $this->setDefaultDisableWith($value, $tagOptions);
        return $this->tag('input', $tagOptions);
    }

    public function buttonTag($content = null, $options = []) {
        $options = array_merge([
            'name' => 'button',
            'type' => 'submit'
        ], $options);

        return $this->contentTag('button', $content ?? 'Button', $options);
    }

    private function extraTagsForForm(&$options) {
        $csrf_token = Arr::pull($options, 'csrf_token');
        $method = strtolower(Arr::pull($options, 'method', 'post'));

        $tags = '';
        switch ($method) {
            case "get":
                $options['method'] = 'get';
                break;
            case "post":
                $options['method'] = 'post';
                $tags .= $this->tokenTag($csrf_token, [
                    'action' => $options['action'],
                    'method' => 'post'
                ]);
                break;
            default:
                $options['method'] = 'post';
                $tags .= $this->methodTag($method) . $this->tokenTag($csrf_token, [
                    'action' => $options['action'],
                    'method' => $method
                ]);
                break;
        }

        if (Arr::pull($options, 'enforce_utf8', true)) {
            $tags = $this->utf8EnforcerTag() . $tags;
        }

        return $tags;
    }

    private function htmlOptionsForForm($urlForOptions, $options) {
        $html = Arr::pull($options, 'html') ?? [];
        $htmlOptions = array_merge(Arr::only($options, ['id', 'class', 'method', 'data']), $html);

        if (!Arr::pull($options, 'local') && static::$formWithGeneratesRemoteForms == true) {
            $htmlOptions['data-remote'] = true;
        }
        if (static::$skipEnforcingUtf8) {
            $htmlOptions['enforce_utf8'] = !static::$skipEnforcingUtf8;
        }

        $htmlOptions['action'] = $this->urlFor($urlForOptions);
        $htmlOptions['accept-charset'] = 'UTF-8';

        if (Arr::get($options, 'data-remote')
            && !static::embedCsrfTokenInRemoteForms && empty($options['csrf_token'])
        ) {
            $htmlOptions['csrf_token'] = false;
        } elseif (Arr::get($options, 'csrf_token') === true) {
            $htmlOptions['csrf_token'] = null;
        }

        return $htmlOptions;
    }

    private function sanitizeToId($name) {
        return preg_replace('/[^-a-zA-Z0-9:\.]/', '_', str_replace(']', '', $name));
    }

    private function methodTag($method) {
        return $this->tag('input', [
            'type' => 'hidden',
            'name' => '_method',
            'value' => $method ?? 'post'
        ], false, false);
    }

    private function tokenTag($token, $options) {
        return $this->tag('input', [
            'type' => 'hidden',
            'name' => '_token',
            'value' => $token ?? csrf_token()
        ], false, false);
    }

    private function utf8EnforcerTag() {
        return new HtmlString('<input type="hidden" name="utf8" value="&#x2713;" />');
    }

    private function setDefaultDisableWith($value, &$options) {
        $data = Arr::get($options, 'data', []);

        if (Arr::get($data, 'disable-with', null) !== false) {
            $data['disable-with'] = Arr::get($data, 'disable-with', $value);
        } else {
            unset($data['disable-with']);
        }

        $options['data'] = $data;
    }
}
