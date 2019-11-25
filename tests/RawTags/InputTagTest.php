<?php

namespace SilvertipSoftware\Forms\Tests\RawTags;

use Illuminate\Support\HtmlString;
use SilvertipSoftware\Forms\Tests\TestCase;

class InputTagTest extends TestCase
{
    use GenericTagTestHelpers;

    public function testItMakesAnHtmlString()
    {
        $result = \Form::textFieldTag('SOMENAME');

        $this->assertInstanceOf(HtmlString::class, $result);
    }

    public function testItMakesAnTextInputTag()
    {
        $result = \Form::textFieldTag('SOMENAME', 'SOMEVALUE');
        $this->assertSeeTag('input', $result);
        $this->assertStringContainsString('type="text"', $result);
        $this->assertStringContainsString('name="SOMENAME"', $result);
        $this->assertStringContainsString('value="SOMEVALUE"', $result);
        $this->assertSeeTagClose('', $result);
    }

    public function testItGeneratesAnId()
    {
        $result = \Form::textFieldTag('SOMENAME', 'SOMEVALUE');

        $this->assertStringContainsString('id="SOMENAME"', $result);
    }

    public function testItSanitizesIdOfBrackets()
    {
        $result = \Form::textFieldTag('object[field]');
        $this->assertStringContainsString('id="object_field"', $result);

        $result = \Form::textFieldTag('object[field][subfield]');
        $this->assertStringContainsString('id="object_field_subfield"', $result);
    }

    public function testItSanitizesIdOfOtherChars()
    {
        $result = \Form::textFieldTag('some@name$');

        $this->assertStringContainsString('id="some_name_"', $result);
    }

    public function testItSanitizesValue()
    {
        $result = \Form::textFieldTag('field', 'quo">te');

        $this->assertStringContainsString('value="quo&quot;&gt;te"', $result);
    }

    public function testNumberTypeHelper()
    {
        $result = \Form::numberFieldTag('field');

        $this->assertStringContainsString('type="number"', $result);
    }

    public function testHiddenTypeHelper()
    {
        $result = \Form::hiddenFieldTag('field');

        $this->assertStringContainsString('type="hidden"', $result);
    }

    protected function makeTagWithOptions($options)
    {
        return \Form::textFieldTag('name', 'value', $options);
    }
}