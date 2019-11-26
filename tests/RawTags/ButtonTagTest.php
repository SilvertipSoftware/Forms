<?php

namespace SilvertipSoftware\Forms\Tests\RawTags;

use SilvertipSoftware\Forms\Tests\TestCase;

class ButtonTagTest extends TestCase
{
    use GenericTagTestHelpers;

    public function testItMakesALabelTag()
    {
        $result = \Form::buttonTag('Action');

        $this->assertSeeTag('button', $result);
        $this->assertStringContainsString('type="submit"', $result);
        $this->assertStringContainsString('name="button"', $result);
        $this->assertStringContainsString('>Action</', $result);
        $this->assertSeeTagClose('button', $result);
    }

    public function testItDefaultsContent()
    {
        $result = \Form::buttonTag();

        $this->assertStringContainsString('>Button</', $result);
    }

    public function testCanSetName()
    {
        $result = \Form::buttonTag('Action', ['name' => 'the_button']);

        $this->assertStringContainsString('name="the_button"', $result);
        $this->assertStringNotContainsString('name="button"', $result);
    }

    protected function makeTagWithOptions($options)
    {
        return \Form::buttonTag('Action', $options);
    }
}
