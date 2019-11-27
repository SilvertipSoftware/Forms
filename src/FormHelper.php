<?php

namespace SilvertipSoftware\Forms;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Traits\ForwardsCalls;

class FormHelper
{
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

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function registerDirectives()
    {
        Blade::directive('formWith', $this->makeFormStarterFunction(false));
        Blade::directive('fieldsFor', $this->makeFormStarterFunction(true));
        Blade::directive('fieldsForWith', $this->makeFormStarterFunction(true, true));

        $ender = function ($expression) {
            return "<?php echo Form::end(); ?>";
        };
        Blade::directive('endForm', $ender);
        Blade::directive('endFieldsFor', $ender);

        // deprecate
        Blade::directive('endFormWith', $ender);
    }

    public function object()
    {
        return $this->currentBuilder()->object;
    }

    public function __call($method, $params)
    {
        $builder = end($this->builders);
        if (!$builder) {
            throw new \Exception('Not inside a form');
        }

        $ret = $this->forwardCallTo($builder, $method, $params);
        return is_object($ret) ? $ret : '';
    }

    private function makeFormStarterFunction($isSubform, $isAtRoot = false)
    {
        $method = $isSubform ? ($isAtRoot ? 'fieldsForWithObject' : 'fieldsFor') : 'with';

        return function ($expression) use ($method) {
            $cleanExpr = trim(Blade::stripParentheses($expression));
            if (preg_match('/(.*)\s+as\s+(\$[a-zA-Z_][a-zA-Z0-9_]*)/', $cleanExpr, $args)) {
                return "<?php echo Form::" . $method . "(" . $args[1] . "); " . $args[2] . " = Form::currentBuilder(); ?>";
            } else {
                return "<?php echo Form::" . $method . "(" . $cleanExpr . "); ?>";
            }
        };
    }
}
