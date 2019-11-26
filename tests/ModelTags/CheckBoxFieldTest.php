<?php

namespace SilvertipSoftware\Forms\Tests\ModelTags;

use Illuminate\Support\HtmlString;
use SilvertipSoftware\Forms\Tests\Post;
use SilvertipSoftware\Forms\Tests\TestCase;

class CheckBoxFieldTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->post = new Post([
            'is_published' => true,
            'state' => 2,
            'keywords' => ['foo', 'bar', 'bat']
        ]);
        $this->options = [
            'object' => $this->post
        ];
    }

    public function testItMakesAnHtmlString()
    {
        $result = \Form::checkBoxWithObject('post', 'is_published', $this->options);

        $this->assertInstanceOf(HtmlString::class, $result);
    }

    public function testItMakesACheckBoxInputTag()
    {
        $result = \Form::checkBoxWithObject('post', 'is_published', $this->options);

        $this->assertSeeTag('input', $result);
        $this->assertStringContainsString('type="checkbox"', $result);
    }

    public function testItChecksIfValueIsTrue()
    {
        $result = \Form::checkBoxWithObject('post', 'is_published', $this->options);

        $this->assertStringContainsString('checked="checked"', $result);
    }

    public function testItDoesNotCheckIfValueIsFalse()
    {
        $this->post->is_published = false;
        $result = \Form::checkBoxWithObject('post', 'is_published', $this->options);

        $this->assertStringNotContainsString('checked="checked"', $result);
    }

    public function testCanProvideValues()
    {
        $result = \Form::checkBoxWithObject('post', 'is_published', $this->options, 'yea', 'nay');

        $this->assertStringContainsString('value="yea"', $result);
        $this->assertStringContainsString('value="nay"', $result);
    }

    public function testComparesAttributeWithProvidedValues()
    {
        $this->post->is_published = 'yea';
        $affirmative = \Form::checkBoxWithObject('post', 'is_published', $this->options, 'yea', 'nay');
        $this->post->is_published = 'nay';
        $negative = \Form::checkBoxWithObject('post', 'is_published', $this->options, 'yea', 'nay');

        $this->assertStringContainsString('checked="checked"', $affirmative);
        $this->assertStringNotContainsString('checked="checked"', $negative);
    }

    public function testComparesIntegerValues()
    {
        $result = \Form::checkBoxWithObject('post', 'state', $this->options, '2', '1');

        $this->assertStringContainsString('checked="checked"', $result);
    }

    public function testArrayInclusion()
    {
        $result = \Form::checkBoxWithObject('post', 'keywords', $this->options, 'bar', 'none');

        $this->assertStringContainsString('checked="checked"', $result);
    }

    public function testCanCheckExplicitly()
    {
        $options = array_merge($this->options, [
            'checked' => true
        ]);
        $result = \Form::checkBoxWithObject('post', 'is_published', $options);

        $this->assertStringContainsString('checked="checked"', $result);
    }

    public function testHandlesMultiple()
    {
        $options = array_merge($this->options, [
            'multiple' => true,
            'include_hidden' => false
        ]);
        $result = \Form::checkBoxWithObject('post', 'keywords', $options, 'foo');

        $this->assertStringContainsString('id="post_keywords_foo"', $result);
    }

    public function testItMakesBackingInput()
    {
        $result = \Form::checkBoxWithObject('post', 'is_published', $this->options);

        $this->assertStringContainsString('type="hidden"', $result);
    }

    public function testCanSuppressBackingInput()
    {
        $options = array_merge($this->options, [
            'include_hidden' => false
        ]);
        $result = \Form::checkBoxWithObject('post', 'is_published', $options);

        $this->assertStringNotContainsString('type="hidden"', $result);
    }
}
