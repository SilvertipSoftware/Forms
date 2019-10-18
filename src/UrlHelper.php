<?php

namespace SilvertipSoftware\Forms;

use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

trait UrlHelper {

    public function urlFor($options = null) {
        if (is_string($options)) {
            return $options;
        } else {
            throw new \Exception('non-string urls (like "back") not supported yet');
        }
    }
}
