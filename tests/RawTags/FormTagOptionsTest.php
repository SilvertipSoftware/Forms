<?php

namespace SilvertipSoftware\Forms\Tests\RawTags;

use SilvertipSoftware\Forms\Tests\TestCase;

class FormTagOptionsTest extends TestCase
{

    public function testCanSetId()
    {
        $htmlOptions = [
            'id' => 'some-form-id-1'
        ];
        $result = \Form::formTag('/abc', ['html' => $htmlOptions]);

        $this->assertStringContainsString('id="' . $htmlOptions['id'] . '"', $result);
    }

    public function testCanSetClass()
    {
        $htmlOptions = [
            'class' => 'some-form-class-1'
        ];
        $result = \Form::formTag('/abc', ['html' => $htmlOptions]);

        $this->assertStringContainsString('class="' . $htmlOptions['class'] . '"', $result);
    }

    public function testCanSetMethod()
    {
        $htmlOptions = [
            'method' => 'put'
        ];
        $result = \Form::formTag('/abc', ['html' => $htmlOptions]);

        $this->assertStringContainsString('method="post"', $result);
        $this->assertStringContainsString('name="_method"', $result);
        $this->assertStringContainsString('value="put"', $result);
    }

    public function testCanSetData()
    {
        $htmlOptions = [
            'data' => [
                'value' => 100,
                'controller' => 'toggle'
            ]
        ];
        $result = \Form::formTag('/abc', ['html' => $htmlOptions]);

        $this->assertStringContainsString('data-value="100"', $result);
        $this->assertStringContainsString('data-controller="toggle"', $result);
    }

    public function testCanSetArbitraryAttributes()
    {
        $htmlOptions = [
            'test-attr' => '123',
        ];
        $result = \Form::formTag('/abc', ['html' => $htmlOptions]);

        $this->assertStringContainsString('test-attr="123"', $result);
    }

    public function testCanMakeNormalForm()
    {
        $options = [
            'local' => true
        ];
        $result = \Form::formTag('/abc', $options);

        $this->assertStringNotContainsString('data-remote="1"', $result);
    }

    public function testCanMakeMultipartForm()
    {
        $options = [
            'multipart' => true
        ];
        $result = \Form::formTag('/abc', $options);

        $this->assertStringContainsString('enctype="multipart/form-data"', $result);
    }

    public function testCanPreventUtf8Marker()
    {
        $htmlOptions = [
            'enforce_utf8' => false
        ];
        $result = \Form::formTag('/abc', ['html' => $htmlOptions]);

        $this->assertStringNotContainsString('name="utf8"', $result);
    }
}