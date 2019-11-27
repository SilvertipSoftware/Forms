<?php

namespace SilvertipSoftware\Forms\Tests\Builder;

use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use SilvertipSoftware\Forms\FormBuilder;
use SilvertipSoftware\Forms\Tests\Author;
use SilvertipSoftware\Forms\Tests\Post;
use SilvertipSoftware\Forms\Tests\TestCase;

class FieldsForWithObjectTest extends TestCase
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
            'name' => 'Hypatia'
        ]);
        $this->options = [
            'url' => '/DUMMY-URL'
        ];

        \Form::with($this->post, $this->options);
    }

    public function testWithoutViewItReturnsNothing()
    {
        $result = \Form::fieldsForWithObject('author', $this->author);

        $this->assertEquals('', $result);
    }

    public function testItCreatesNewBuilder()
    {
        $rootBuilder = \Form::currentBuilder();
        \Form::fieldsForWithObject('author', $this->author);
        $builder = \Form::currentBuilder();

        $this->assertNotEquals($rootBuilder, $builder);
    }

    public function testItSetsBuilderScopeCorrectly()
    {
        \Form::fieldsForWithObject('author', $this->author);
        $builder = \Form::currentBuilder();

        $this->assertEquals('author', $builder->objectName);
    }

    public function testCanNamespaceFieldIds()
    {
        \Form::fieldsForWithObject('author', $this->author, ['namespace' => 'ns']);
        $result = \Form::textField('title');

        $this->assertStringContainsString('id="ns_author_title"', $result);
    }

    public function testCanIndexFields()
    {
        \Form::fieldsForWithObject('author', $this->author, ['index' => '5']);
        $result = \Form::textField('title');

        $this->assertStringContainsString('name="author[5][title]"', $result);
        $this->assertStringContainsString('id="author_5_title"', $result);
    }

    public function testItOnlyPopsBuilderWhenEndingSubform()
    {
        $rootBuilder = \Form::currentBuilder();
        \Form::fieldsForWithObject('author', $this->author);
        \Form::end();
        $builder = \Form::currentBuilder();

        $this->assertEquals($rootBuilder, $builder);
    }

    public function testItEvaluatesSpecifiedView()
    {
        $result = \Form::fieldsForWithObject('author', $this->author, ['view' => 'static']);

        $this->assertStringContainsString('view contents', $result);
    }

    public function testItPassesObjectToView()
    {
        $result = \Form::fieldsForWithObject('author', $this->author, ['view' => 'author_name']);

        $this->assertStringContainsString('Hypatia', $result);
    }
}
