<?php

namespace SilvertipSoftware\Forms\Tests\Builder;

use Illuminate\Support\HtmlString;
use SilvertipSoftware\Forms\FormBuilder;
use SilvertipSoftware\Forms\Tests\Author;
use SilvertipSoftware\Forms\Tests\Post;
use SilvertipSoftware\Forms\Tests\TestCase;

class FormWithModelTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->post = new Post([
            'id' => 123,
            'title' => 'First Post'
        ]);
        $this->author = new Author([
            'id' => 654,
            'title' => 'Hypatia'
        ]);
        $this->options = [
            'url' => '/DUMMY-URL'
        ];
    }

    public function testItReturnsFormTag()
    {
        $result = \Form::with($this->post, $this->options);

        $this->assertInstanceOf(HtmlString::class, $result);
        $this->assertSeeTag('form', $result);
        $this->assertStringContainsString('action="' . $this->options['url'] . '"', $result);
    }

    public function testCanProvideUrl()
    {
        $options = array_merge($this->options, [
            'url' => '/some-other-url'
        ]);
        $result = \Form::with($this->post, $options);
        $this->assertStringContainsString('action="' . $options['url'] . '"', $result);
    }

    public function testItStartsWithNoBuilders()
    {
        $builder = \Form::currentBuilder();

        $this->assertFalse($builder);
    }

    public function testItCreatesABuilder()
    {
        \Form::with($this->post, $this->options);
        $builder = \Form::currentBuilder();

        $this->assertNotNull($builder);
    }

    public function testBuilderHasModelReference()
    {
        \Form::with($this->post, $this->options);
        $builder = \Form::currentBuilder();

        $this->assertEquals($this->post, $builder->object);
    }

    public function testItCanTakeModelAsOption()
    {
        $options = array_merge($this->options, [
            'model' => $this->post
        ]);
        \Form::with($options);
        $builder = \Form::currentBuilder();

        $this->assertEquals($this->post, $builder->object);
    }

    public function testItDiscoversScope()
    {
        \Form::with($this->post, $this->options);
        $builder = \Form::currentBuilder();

        $this->assertEquals('post', $builder->objectName);
    }

    public function testItCanTakeScopeAsOption()
    {
        $options = array_merge($this->options, [
            'scope' => 'the_object_scope'
        ]);
        \Form::with($options);
        $builder = \Form::currentBuilder();

        $this->assertEquals($options['scope'], $builder->objectName);
    }

    public function testCanEndTheForm()
    {
        \Form::with($this->post, $this->options);
        $result = \Form::end();
        $builder = \Form::currentBuilder();

        $this->assertFalse($builder);
        $this->assertStringContainsString('</form>', $result);
    }
}
