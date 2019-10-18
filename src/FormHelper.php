<?php

namespace SilvertipSoftware\Forms;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Traits\ForwardsCalls;

class FormHelper {
    use Concerns\MakesFormBuilders,
        Concerns\MakesModelTags,
        ForwardsCalls,
        ModelUtils,
        TagHelper,
        UrlHelper,
        FormTagHelper;

    protected $app;

    protected $builders = [];

    public static $defaultFormBuilderClass = FormBuilder::class;
    public static $skipEnforcingUtf8 = false;
    public static $formWithGeneratesIds = true;
    public static $formWithGeneratesRemoteForms = true;
    public static $embedCsrfInRemoteForms = true;
    public static $relationsReturningCollections = [
        'BelongsToMany', 'HasMany', 'HasManyThrough', 'MorphMany', 'MorphToMany'
    ];

    public function __construct($app) {
        $this->app = $app;
    }

    public function registerDirectives() {
        Blade::directive('formWith', function ($expression) {
            $args = [];
            if (preg_match('/(.*)\s+as\s+([a-zA-Z_][a-zA-Z0-9_]*)/', Blade::stripParentheses($expression), $args)) {
                return "<?php echo Form::with(" . $args[1] . "); $" . $args[2] . " = Form::currentBuilder(); ?>";
            }
        });

        Blade::directive('endFormWith', function ($expression) {
            return "<?php echo Form::end(); ?>";
        });
    }

    public function object() {
        return $this->currentBuilder()->object;
    }

    public function __call($method, $params) {
        $builder = end($this->builders);
        if (!$builder) {
            return;
        }

        $ret = $this->forwardCallTo($builder, $method, $params);
        return is_object($ret) ? $ret : '';
    }
}
