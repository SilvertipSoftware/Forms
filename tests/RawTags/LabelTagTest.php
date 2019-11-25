<?php

namespace SilvertipSoftware\Forms\Tests\RawTags;

use Illuminate\Support\HtmlString;
use SilvertipSoftware\Forms\Tests\TestCase;

class LabelTagTest extends TestCase
{
    use GenericTagTestHelpers;

    public function testItMakesAnHtmlString()
    {
        $result = \Form::labelTag('field');

        $this->assertInstanceOf(HtmlString::class, $result);
    }

    public function testItMakesALabelTag()
    {
        $result = \Form::labelTag('field', 'Content');

        $this->assertSeeTag('label', $result);
        $this->assertStringContainsString('for="field"', $result);
        $this->assertStringContainsString('>Content</', $result);
        $this->assertSeeTagClose('label', $result);
    }

    public function testItSanitizesForAttributeOfBrackets()
    {
        $result = \Form::labelTag('object[field]');

        $this->assertStringContainsString('for="object_field"', $result);
    }

    public function testItSanitizesForAttributeOfOtherChars()
    {
        $result = \Form::labelTag('<some@name$');

        $this->assertStringContainsString('for="_some_name_"', $result);
    }

    public function testItTakesForAttributeAsOption()
    {
        $result = \Form::labelTag(null, null, ['for' => 'field']);

        $this->assertStringContainsString('for="field"', $result);
    }

    public function testItWorksWithoutName()
    {
        $result = \Form::labelTag(null);

        $this->assertStringContainsString('<label></label>', $result);
    }

    public function testItDefaultsContent()
    {
        $result = \Form::labelTag('username');

        $this->assertStringContainsString('>Username</label>', $result);
    }

    public function testDefaultContentIsSlugged()
    {
        $result = \Form::labelTag('user name');

        $this->assertStringContainsString('>User-Name</label>', $result);
    }

    protected function makeTagWithOptions($options)
    {
        return \Form::labelTag('name', 'Content', $options);
    }
}