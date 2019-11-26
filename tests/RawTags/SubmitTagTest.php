<?php

namespace SilvertipSoftware\Forms\Tests\RawTags;

use SilvertipSoftware\Forms\Tests\TestCase;

class SubmitTagTest extends TestCase
{
    use GenericTagTestHelpers;

    public function testItMakesALabelTag()
    {
        $result = \Form::submitTag('Save');

        $this->assertSeeTag('input', $result);
        $this->assertStringContainsString('type="submit"', $result);
        $this->assertStringContainsString('name="commit"', $result);
        $this->assertStringContainsString('value="Save"', $result);
        $this->assertSeeTagClose('', $result);
    }

    public function testItDefaultsDisableWithText()
    {
        $result = \Form::submitTag('Save');

        $this->assertStringContainsString('data-disable-with="Save"', $result);
    }

    public function testItTakesDisableWithTextAsOption()
    {
        $options = [
            'data' => [
                'disable-with' => "Saving..."
            ]
        ];
        $result = \Form::submitTag('Save', $options);

        $this->assertStringContainsString('data-disable-with="Saving..."', $result);
    }

    public function testCanDisableDisableWithText()
    {
        $options = [
            'data' => [
                'disable-with' => false
            ]
        ];
        $result = \Form::submitTag('Save', $options);

        $this->assertStringNotContainsString('data-disable-with', $result);
    }

    public function testItEscapesValueTextAndDisableWithText()
    {
        $options = [
            'data' => [
                'disable-with' => "Sa>ving..."
            ]
        ];
        $result = \Form::submitTag('Sa<ve', $options);

        $this->assertStringNotContainsString('Sa&gt;ve', $result);
        $this->assertStringNotContainsString('Sa&lt;ving...', $result);
    }

    protected function makeTagWithOptions($options)
    {
        return \Form::submitTag('Save', $options);
    }
}
