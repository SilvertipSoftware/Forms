<?php

namespace SilvertipSoftware\Forms\Tests\Builder;

use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use SilvertipSoftware\Forms\Tests\Author;
use SilvertipSoftware\Forms\Tests\Comment;
use SilvertipSoftware\Forms\Tests\Post;
use SilvertipSoftware\Forms\Tests\TestCase;

class FieldsForTest extends TestCase
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
        $this->comment = new Comment([
            'id' => 999,
            'body' => 'interesting'
        ]);
        $this->formOptions = [
            'url' => '/dummy-url'
        ];
        $this->options = [
            'view' => 'author_subform'
        ];
    }

    public function testWithoutViewItReturnsNothing()
    {
        \Form::with($this->post, $this->formOptions);
        $result = \Form::fieldsFor('author', $this->author);

        $this->assertEquals('', $result);
    }

    public function testItCreatesNewBuilder()
    {
        \Form::with($this->post, $this->formOptions);
        $rootBuilder = \Form::currentBuilder();
        \Form::fieldsFor('author', $this->author);
        $builder = \Form::currentBuilder();

        $this->assertNotEquals($rootBuilder, $builder);
    }

    public function testItReturnsAnHtmlString()
    {
        \Form::with($this->post, $this->formOptions);
        $result = \Form::fieldsFor('author', $this->author, $this->options);

        $this->assertInstanceOf(HtmlString::class, $result);
    }

    public function testItScopesFieldsCorrectlyInSimpleCase()
    {
        \Form::with($this->post, $this->formOptions);
        $result = \Form::fieldsFor('author', $this->author, $this->options);

        $this->assertStringContainsString('name="post[author][name]"', $result);
    }

    public function testItRespectsGivenScope()
    {
        \Form::with($this->post, $this->formOptions);
        $result = \Form::fieldsFor('writer', $this->author, $this->options);

        $this->assertStringContainsString('name="post[writer][name]"', $result);
    }

    public function testItCanUseKeysAsIndices()
    {
        \Form::with($this->post, $this->formOptions);
        $result = \Form::fieldsFor('author[]', $this->author, $this->options);

        $this->assertStringContainsString('name="post[author][654][name]"', $result);
    }

    public function testItUsesParentIndex()
    {
        $formOptions = array_merge($this->formOptions, [
            'index' => 'foo'
        ]);
        \Form::with($this->post, $formOptions);
        $result = \Form::fieldsFor('author', $this->author, $this->options);

        $this->assertStringContainsString('name="post[foo][author][name]"', $result);
    }

    public function testItCanUseParentAutoIndexing()
    {
        $formOptions = array_merge($this->formOptions, [
            'scope' => 'post[]'
        ]);
        \Form::with($this->post, $formOptions);
        $result = \Form::fieldsFor('author', $this->author, $this->options);

        $this->assertStringContainsString('name="post[123][author][name]"', $result);
    }

    public function testItHandlesNestedAttributes()
    {
        $this->post->_acceptNestedAttributes = true;
        \Form::with($this->post, $this->formOptions);
        $result = \Form::fieldsFor('author', $this->author, $this->options);

        $this->assertStringContainsString('name="post[author_attributes][name]"', $result);
    }

    public function testItHandlesNestedManyAttributesSingly()
    {
        $this->post->_acceptNestedAttributes = true;
        \Form::with($this->post, $this->formOptions);
        $result = \Form::fieldsFor('comments', $this->comment, [
            'view' => 'comment_subform'
        ]);

        $this->assertStringContainsString('name="post[comments_attributes][0][body]"', $result);
    }

    public function testItIncludesHiddenIdFieldForSavedNestedModels()
    {
        $this->post->_acceptNestedAttributes = true;
        \Form::with($this->post, $this->formOptions);
        $this->comment->exists = true;
        $result = \Form::fieldsFor('comments', $this->comment, [
            'view' => 'comment_subform'
        ]);

        $this->assertStringContainsString('type="hidden"', $result);
        $this->assertStringContainsString('value="999"', $result);
    }

    public function testItHandlesNestedManyAttributesMultiply()
    {
        $this->post->_acceptNestedAttributes = true;
        \Form::with($this->post, $this->formOptions);
        $comments = new Collection([
            $this->comment,
            new Comment(['body' => 'new'])
        ]);
        $result = \Form::fieldsFor('comments', $comments, [
            'view' => 'comment_subform'
        ]);

        $this->assertStringContainsString('name="post[comments_attributes][0][body]"', $result);
        $this->assertStringContainsString('name="post[comments_attributes][1][body]"', $result);
    }

    public function testItHandlesCallableChildIndex()
    {
        $this->post->_acceptNestedAttributes = true;
        \Form::with($this->post, $this->formOptions);
        $comments = new Collection([
            $this->comment,
            new Comment(['body' => 'new'])
        ]);
        $result = \Form::fieldsFor('comments', $comments, [
            'view' => 'comment_subform',
            'child_index' => function($index) { return $this->id ?? 'browser-'.$index; }
        ]);

        $this->assertStringContainsString('name="post[comments_attributes][999][body]"', $result);
        $this->assertStringContainsString('name="post[comments_attributes][browser-1][body]"', $result);
    }

    public function testCanSpecifyAChildIndex()
    {
        $this->post->_acceptNestedAttributes = true;
        \Form::with($this->post, $this->formOptions);
        $result = \Form::fieldsFor('comments', $this->comment, [
            'view' => 'comment_subform',
            'child_index' => 'REPL'
        ]);

        $this->assertStringContainsString('name="post[comments_attributes][REPL][body]"', $result);
    }

    public function testItCanTakeJustAModel()
    {
        \Form::with($this->post, $this->formOptions);
        $result = \Form::fieldsFor($this->author, $this->options);

        $this->assertStringContainsString('name="post[author][name]"', $result);
        $this->assertStringContainsString('value="' . $this->author->name . '"', $result);
    }
}
