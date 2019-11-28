<?php

namespace SilvertipSoftware\Forms\Tests\ModelTags;

use Illuminate\Support\HtmlString;
use SilvertipSoftware\Forms\Tests\Post;
use SilvertipSoftware\Forms\Tests\TestCase;

class TextFieldTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->post = new Post([
            'title' => 'First Post'
        ]);
        $this->options = [
            'object' => $this->post
        ];
    }

    public function testItMakesAnHtmlString()
    {
        $result = \Form::textFieldWithObject('post', 'title', $this->options);

        $this->assertInstanceOf(HtmlString::class, $result);
    }

    public function testItMakesATextInputTag()
    {
        $result = \Form::textFieldWithObject('post', 'title', $this->options);

        $this->assertSeeTag('input', $result);
        $this->assertStringContainsString('type="text"', $result);
        $this->assertStringContainsString('/>', $result);
    }

    public function testItSetsDefaultNameAndId()
    {
        $result = \Form::textFieldWithObject('post', 'title', $this->options);

        $this->assertStringContainsString('name="post[title]"', $result);
        $this->assertStringContainsString('id="post_title"', $result);
    }

    public function testCanSupplyNameAndId()
    {
        $options = array_merge($this->options, [
            'name' => 'field',
            'id' => 'field1'
        ]);
        $result = \Form::textFieldWithObject('post', 'title', $options);

        $this->assertStringContainsString('name="field"', $result);
        $this->assertStringContainsString('id="field1"', $result);
    }

    public function testCanNamespaceId()
    {
        $options = array_merge($this->options, [
            'namespace' => 'foo'
        ]);
        $result = \Form::textFieldWithObject('post', 'title', $options);

        $this->assertStringContainsString('name="post[title]"', $result);
        $this->assertStringContainsString('id="foo_post_title"', $result);
    }

    public function testCanSupplyType()
    {
        $options = array_merge($this->options, [
            'type' => 'tel'
        ]);
        $result = \Form::textFieldWithObject('post', 'title', $options);

        $this->assertStringContainsString('type="tel"', $result);
    }

    public function testItSetsValueFromAttribute()
    {
        $result = \Form::textFieldWithObject('post', 'title', $this->options);

        $this->assertStringContainsString('value="First Post"', $result);
    }

    public function testItSetsValueFromMethod()
    {
        $result = \Form::textFieldWithObject('post', 'aFunc', $this->options);

        $this->assertStringContainsString('value="' . $this->post->aFunc() . '"', $result);
    }

    public function testItSetsValueFromCamelCaseMethod()
    {
        $result = \Form::textFieldWithObject('post', 'a_func', $this->options);

        $this->assertStringContainsString('value="' . $this->post->aFunc() . '"', $result);
    }

    public function testCanSupplyValue()
    {
        $options = array_merge($this->options, [
            'value' => random_int(1, 100000)
        ]);
        $result = \Form::textFieldWithObject('post', 'title', $options);

        $this->assertStringContainsString('value="' . $options['value'] . '"', $result);
    }

    public function testItWorksWithIndexedObjectName()
    {
        $result = \Form::textFieldWithObject('post[1]', 'title', $this->options);

        $this->assertStringContainsString('name="post[1][title]"', $result);
        $this->assertStringContainsString('id="post_1_title"', $result);
    }

    public function testItWorksWithIndexOption()
    {
        $options = array_merge($this->options, [
            'index' => 5
        ]);
        $result = \Form::textFieldWithObject('post', 'title', $options);

        $this->assertStringContainsString('name="post[5][title]"', $result);
        $this->assertStringContainsString('id="post_5_title"', $result);
    }

    public function testItWorksWithArray()
    {
        $result = \Form::textFieldWithObject('array', 'attr', [
            'object' => [
                'attr' => 'abc'
            ]
        ]);

        $this->assertStringContainsString('value="abc"', $result);
    }

    public function testCanDisableIdGeneration()
    {
        $options = array_merge($this->options, [
            'skip_default_ids' => true
        ]);
        $result = \Form::textFieldWithObject('post', 'title', $options);

        $this->assertStringNotContainsString('id="', $result);
    }
}
