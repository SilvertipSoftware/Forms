<?php

namespace SilvertipSoftware\Forms\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use SilvertipSoftware\Forms\FormHelper;

class TestCase extends OrchestraTestCase
{

    protected function getPackageAliases($app)
    {
        return [
            'Form' => 'SilvertipSoftware\Forms\Facades\Form'
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app->singleton(FormHelper::class, function ($app) {
            return new FormHelper($app);
        });

        $app['config']->set('view.paths', ['tests/views']);
    }

    protected function assertSeeTag($tag, $result) {
        $this->assertMatchesRegularExpression('/\<' . $tag . '([^a-z]|\/\>)/', $result);
    }

    protected function assertSeeTagClose($tag, $result) {
        $this->assertMatchesRegularExpression('/<?\/' . $tag . '>/', $result);
    }
}