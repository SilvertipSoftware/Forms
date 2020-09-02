<?php

namespace SilvertipSoftware\Forms\Tests\ModelTags;

use Illuminate\Support\HtmlString;
use SilvertipSoftware\Forms\Tests\Post;
use SilvertipSoftware\Forms\Tests\TestCase;

class RadioButtonFieldTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->post = new Post([
            'title' => 'Some Post',
            'state' => 1
        ]);
        $this->options = [
            'object' => $this->post
        ];
    }

    public function testItMakesAnHtmlString()
    {
        $result = \Form::radioButtonWithObject('post', 'state', 1, $this->options);

        $this->assertInstanceOf(HtmlString::class, $result);
    }

    public function testItMakesARadioButtonInputTag()
    {
        $result = \Form::radioButtonWithObject('post', 'state', 1, $this->options);

        $this->assertSeeTag('input', $result);
        $this->assertStringContainsString('type="radio"', $result);
    }

    public function testItChecksIfValueIsEqual()
    {
        $result = \Form::radioButtonWithObject('post', 'state', 1, $this->options);

        $this->assertStringContainsString('checked="checked"', $result);
    }

    public function testItDoesNotCheckIfValueIsDifferent()
    {
        $this->post->state = 2;
        $result = \Form::radioButtonWithObject('post', 'state', 1, $this->options);

        $this->assertStringNotContainsString('checked="checked"', $result);
    }

    public function testCanProvideValues()
    {
        $result = \Form::radioButtonWithObject('post', 'state', 100, $this->options);

        $this->assertStringContainsString('value="100"', $result);
    }

    public function testCanCheckExplicitly()
    {
        $options = array_merge($this->options, [
            'checked' => true
        ]);
        $result = \Form::radioButtonWithObject('post', 'state', 100, $options);

        $this->assertStringContainsString('checked="checked"', $result);
    }
}
