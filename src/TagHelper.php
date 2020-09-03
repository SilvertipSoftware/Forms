<?php

namespace SilvertipSoftware\Forms;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

trait TagHelper {

    public function tag($name = null, $options = null, $open = false, $mustEscape = true) {
        if ($name == null) {
            return $this->newTagBuilder();
        } else {
            return new HtmlString(
                '<' . $name . $this->newTagBuilder()->tagOptions($options, $mustEscape)
                . ($open ? '>' : '/>')
            );
        }
    }

    public function closeTag($name) {
        return new HtmlString('</' . $name . '>');
    }

    public function contentTag($name, $contentOrOptions, $options = null, $mustEscape = true, $block = null) {
        if ($block instanceof Closure) {
            if (is_array($contentOrOptions)) {
                $options = $contentOrOptions;
            }
            return $this->newTagBuilder()->contentTagString($name, $block->call($this), $options, $mustEscape);
        }

        return $this->newTagBuilder()->contentTagString($name, $contentOrOptions, $options, $mustEscape);
    }

    public function cdataSection($content) {
        $content = preg_replace('/\]\]\>/', ']]]]><!CDATA[>', $content);

        return new HtmlString('<![CDATA[' . $content . ']]>');
    }

    public function escapeOnce($html) {
        throw new \Exception("TBD");
    }

    private function newTagBuilder() {
        return new class() {
            public function tagString($name, $content = null, $options = []) {
                $mustEscape = Arr::pull($options, 'escape_attributes', true);

                if (in_array($name, static::VOID_ELEMENTS) && empty($content)) {
                    return new HtmlString('<' . Str::kebab($name) . $this->tagOptions($options, $mustEscape) . '>');
                } else {
                    return $this->contentTagString(Str::kebab($name), $content ?? '', $options, $mustEscape);
                }
            }

            public function contentTagString($name, $content, $options, $mustEscape) {
                $tagOptions = $this->tagOptions($options, $mustEscape);

                if ($mustEscape) {
                    $content = e($content);
                }

                return new HtmlString(
                    '<' . $name . $tagOptions . '>'
                    . Arr::get(static::PRE_CONTENT_STRINGS, $name, '') . $content
                    . '</' . $name . '>'
                );
            }

            public function tagOptions($options, $mustEscape = true) {
                if (empty($options)) {
                    return '';
                }

                $output = '';
                $sep = ' ';
                foreach ($options as $key => $value) {
                    if (in_array($key, static::TAG_PREFIXES) && is_array($value)) {
                        foreach ($value as $k => $v) {
                            if ($v !== null) {
                                $output .= $sep . $this->prefixTagOption($key, $k, $v, $mustEscape);
                            }
                        }
                    } elseif (in_array($key, static::BOOLEAN_ATTRIBUTES)) {
                        if ($value) {
                            $output .= $sep . $this->booleanTagOption($key);
                        }
                    } elseif ($value !== null) {
                        $output .= $sep . $this->tagOption($key, $value, $mustEscape);
                    }
                }

                return $output;
            }

            public function booleanTagOption($key) {
                return $key . '="' . $key . '"';
            }

            public function tagOption($key, $value, $mustEscape) {
                if (is_array($value)) {
                    $value = implode(' ', array_map(function ($v) use ($mustEscape) {
                        return $mustEscape ? e($v) : $v;
                    }, $value));
                } else {
                    $value = $mustEscape ? e($value) : $value;
                }

                $value = preg_replace('/"/', '&quot;', $value);
                return $key . '="' . $value . '"';
            }

            protected function prefixTagOption($prefix, $key, $value, $mustEscape) {
                $key = $prefix . '-' . Str::kebab($key);
                if (!(is_string($value) || is_numeric($value))) {
                    $value = json_encode($value);
                }

                return $this->tagOption($key, $value, $mustEscape);
            }

            public function __call($method, $args) {
                return $this->tagString($method, ...$args);
            }

            private const BOOLEAN_ATTRIBUTES = [
                'allowfullscreen', 'async', 'autofocus', 'autoplay', 'checked',
                'compact', 'controls', 'declare', 'default', 'defaultchecked',
                'defaultmuted', 'defaultselected', 'defer', 'disabled',
                'enabled', 'formnovalidate', 'hidden', 'indeterminate', 'inert',
                'ismap', 'itemscope', 'loop', 'multiple', 'muted', 'nohref',
                'noresize', 'noshade', 'novalidate', 'nowrap', 'open',
                'pauseonexit', 'readonly', 'required', 'reversed', 'scoped',
                'seamless', 'selected', 'sortable', 'truespeed', 'typemustmatch',
                'visible',
            ];

            private const TAG_PREFIXES = ['aria', 'data', 'v'];

            private const PRE_CONTENT_STRINGS = [];

            private const VOID_ELEMENTS = [
                'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input',
                'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr'
            ];
        };
    }
}
