<?php

namespace SilvertipSoftware\Forms\Tests\RawTags;

use SilvertipSoftware\Forms\Tests\TestCase;

class CustomTagTest extends TestCase
{
    use GenericTagTestHelpers;

    public function makeTagWithOptions($options)
    {
        return \Form::tag('custom-tag', $options);
    }

    public function testItMakesCustomTag()
    {
        $result = \Form::tag('custom-tag');

        $this->assertSeeTag('custom-tag', $result);
    }

    public function testItIsClosedByDefault()
    {
        $result = \Form::tag('custom-tag');

        $this->assertStringContainsString('<custom-tag/>', $result);
    }

    public function testItCanBeMadeOpen()
    {
        $result = \Form::tag('custom-tag', [], true);

        $this->assertEquals('<custom-tag>', $result);
    }

    public function testItCanTakePreEscapedOptions()
    {
        $options = [
            'attr' => 'va&quot;lue'
        ];
        $result = \Form::tag('custom-tag', $options, false, false);

        $this->assertStringContainsString('attr="va&quot;lue"', $result);
    }

    public function testItCanCloseTag()
    {
        $result = \Form::closeTag('custom-tag');

        $this->assertEquals('</custom-tag>', $result);
    }

    public function testItCanMakeContentTags()
    {
        $result = \Form::contentTag('custom-tag', 'Content');

        $this->assertEquals('<custom-tag>Content</custom-tag>', $result);
    }

    public function testItCanGetContentFromFunction()
    {
        $result = \Form::contentTag('custom-tag', '', [], true, function () {
            return 'Content';
        });

        $this->assertEquals('<custom-tag>Content</custom-tag>', $result);
    }

    public function testItCanReturnATagBuilder()
    {
        $result = \Form::tag();

        $this->assertIsObject($result);
        $this->assertTrue(method_exists($result, 'tagString'));
    }
}
