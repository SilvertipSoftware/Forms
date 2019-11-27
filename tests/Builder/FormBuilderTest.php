<?php

namespace SilvertipSoftware\Forms\Tests\Builder;

use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use SilvertipSoftware\Forms\FormBuilder;
use SilvertipSoftware\Forms\Tests\Author;
use SilvertipSoftware\Forms\Tests\Post;
use SilvertipSoftware\Forms\Tests\TestCase;

class FormBuilderTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->post = new Post([
            'id' => 123,
            'title' => 'First Post',
            'rating' => 6,
            'secret' => 'foo',
            'is_published' => 1
        ]);
        $this->allAuthors = new Collection([
            new Author(['id' => 1, 'name' => 'Dante']),
            new Author(['id' => 2, 'name' => 'Hypatia'])
        ]);
        $this->options = [
            'url' => '/DUMMY-URL'
        ];
        \Form::with($this->post, $this->options);
        $this->builder = \Form::currentBuilder();
    }

    public function testItBuildsTextFieldsCorrectly()
    {
        $result = $this->builder->textField('title');

        $this->assertInstanceOf(HtmlString::class, $result);
        $this->assertSeeTag('input', $result);
        $this->assertStringContainsString('name="post[title]"', $result);
        $this->assertStringContainsString('type="text"', $result);
        $this->assertStringContainsString('value="' . $this->post->title . '"', $result);
    }

    public function testItBuildsNumberFieldsCorrectly()
    {
        $result = $this->builder->numberField('rating');

        $this->assertInstanceOf(HtmlString::class, $result);
        $this->assertSeeTag('input', $result);
        $this->assertStringContainsString('name="post[rating]"', $result);
        $this->assertStringContainsString('type="number"', $result);
        $this->assertStringContainsString('value="' . $this->post->rating . '"', $result);
    }

    public function testItBuildsPasswordFieldsCorrectly()
    {
        $result = $this->builder->passwordField('secret');

        $this->assertInstanceOf(HtmlString::class, $result);
        $this->assertSeeTag('input', $result);
        $this->assertStringContainsString('name="post[secret]"', $result);
        $this->assertStringContainsString('type="password"', $result);
        $this->assertStringContainsString('value="' . $this->post->secret . '"', $result);
    }

    public function testItBuildsHiddenFieldsCorrectly()
    {
        $result = $this->builder->hiddenField('secret');

        $this->assertInstanceOf(HtmlString::class, $result);
        $this->assertSeeTag('input', $result);
        $this->assertStringContainsString('name="post[secret]"', $result);
        $this->assertStringContainsString('type="hidden"', $result);
        $this->assertStringContainsString('value="' . $this->post->secret . '"', $result);
    }

    public function testItBuildsCheckBoxesCorrectly()
    {
        $result = $this->builder->checkBox('is_published');

        $this->assertInstanceOf(HtmlString::class, $result);
        $this->assertSeeTag('input', $result);
        $this->assertStringContainsString('name="post[is_published]"', $result);
        $this->assertStringContainsString('type="checkbox"', $result);
        $this->assertStringContainsString('checked="checked"', $result);
    }

    public function testItBuildsLabelsCorrectly()
    {
        $result = $this->builder->label('is_published');

        $this->assertInstanceOf(HtmlString::class, $result);
        $this->assertSeeTag('label', $result);
        $this->assertStringContainsString('for="post_is_published"', $result);
        $this->assertStringContainsString('>Is Published</label>', $result);
    }

    public function testItBuildsLabelsWithTranslationsCorrectly()
    {
        app('translator')->addLines([
            'posts.is_published' => 'In Print',
        ], 'en');
        $result = $this->builder->label('is_published');

        $this->assertInstanceOf(HtmlString::class, $result);
        $this->assertSeeTag('label', $result);
        $this->assertStringContainsString('for="post_is_published"', $result);
        $this->assertStringContainsString('>In Print</label>', $result);
    }

    public function testItBuildsCollectionSelectsCorrectly()
    {
        $result = $this->builder->collectionSelect('author_id', $this->allAuthors, 'id', 'name');

        $this->assertInstanceOf(HtmlString::class, $result);
        $this->assertSeeTag('select', $result);
        $this->assertSeeTag('option', $result);
        $this->assertStringContainsString('name="post[author_id]"', $result);
    }

    public function testItBuildsSubmitCorrectly()
    {
        $result = $this->builder->submit();

        $this->assertInstanceOf(HtmlString::class, $result);
        $this->assertSeeTag('input', $result);
        $this->assertStringContainsString('type="submit"', $result);
    }

    public function testItProvidesDefaultSubmitMessageForNewModel()
    {
        $result = $this->builder->submit();

        $this->assertStringContainsString('value="Create"', $result);
    }

    public function testItProvidesDefaultSubmitMessageForExistingModel()
    {
        $this->post->exists = true;
        $result = $this->builder->submit();

        $this->assertStringContainsString('value="Update"', $result);
    }

    public function testItTranslatesSubmitMessages()
    {
        app('translator')->addLines([
            'posts.submit.create' => 'Shout It Out',
            'posts.update' => 'Fix Typos' // alternate key
        ], 'en');
        $result = $this->builder->submit();
        $this->assertStringContainsString('value="Shout It Out"', $result);

        $this->post->exists = true;
        $result = $this->builder->submit();
        $this->assertStringContainsString('value="Fix Typos"', $result);
    }
}