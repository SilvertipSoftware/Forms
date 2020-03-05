<?php

namespace SilvertipSoftware\Forms\Tests\Directives;

use SilvertipSoftware\Forms\Tests\Author;
use SilvertipSoftware\Forms\Tests\Comment;
use SilvertipSoftware\Forms\Tests\Post;
use SilvertipSoftware\Forms\Tests\TestCase;

class DirectivesTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        \Form::registerDirectives();

        $this->post = new Post([
            'id' => 123,
            'title' => 'First Post',
            'rating' => 6
        ]);
        $this->post->_acceptNestedAttributes = true;
        $this->author = new Author([
            'id' => 654,
            'name' => 'Hypatia'
        ]);
        $this->post->exists = true;
        $this->post->setRelation('author', $this->author);
        $this->viewData = [
            'post' => $this->post,
            'comments' => collect([
                new Comment(['id'=>999, 'body' => 'First Comment']),
                new Comment(['id'=>1000, 'body' => 'Second Comment'])
            ])
        ];
        $this->viewData['comments'][0]->exists = true;
    }

    public function testBigFormTopLevel()
    {
        $result = view('big_form', $this->viewData)->render();

        $this->assertIsString($result);
        $this->assertSeeTag('form', $result);
        $this->assertEquals(1, substr_count($result, '<form'));
        $this->assertStringContainsString('value="patch"', $result);
        $this->assertStringContainsString('name="post[title]"', $result);
        $this->assertStringContainsString('value="First Post"', $result);
        $this->assertStringContainsString('name="post[rating]"', $result);
        $this->assertStringContainsString('value="6"', $result);
        $this->assertStringContainsString('name="post[author_attributes][name]"', $result);
        $this->assertStringContainsString('value="Hypatia"', $result);
        $this->assertStringContainsString('name="post[is_published]"', $result);
        $this->assertStringContainsString('value="1"', $result);
        $this->assertStringNotContainsString('checked="checked"', $result);
    }

    public function testBigFormNested()
    {
        $result = view('big_form', $this->viewData)->render();

        $this->assertStringContainsString('name="post[comments_attributes][0][body]"', $result);
        $this->assertStringContainsString('name="post[comments_attributes][0][id]"', $result);
        $this->assertStringContainsString('value="999"', $result);
        $this->assertStringContainsString('name="post[comments_attributes][1][body]"', $result);
        $this->assertStringNotContainsString('name="post[comments_attributes][1][id]"', $result);
    }

    public function testViewBasedSubForm()
    {
        $result = view('view_based_subform', $this->viewData)->render();

        $this->assertStringContainsString('name="post[comments_attributes][0][body]"', $result);
        $this->assertStringContainsString('name="post[comments_attributes][0][id]"', $result);
        $this->assertStringContainsString('value="999"', $result);
        $this->assertStringContainsString('name="post[comments_attributes][1][body]"', $result);
        $this->assertStringNotContainsString('name="post[comments_attributes][1][id]"', $result);
    }

    public function testLoopedCollection()
    {
        $result = view('looped_collection', $this->viewData)->render();

        $this->assertStringContainsString('name="comment[999][body]"', $result);
        $this->assertStringNotContainsString('comment[999][id]', $result);
        $this->assertStringNotContainsString('value="999"', $result);
        $this->assertStringContainsString('name="comment[1000][body]"', $result);
    }

    private function renderFormWithOptions($options=[])
    {
        $options = array_merge([
            'url' => '/test-route'
        ], $options);

        return view('form_with_options', [
            'options' => $options
        ])->render();
    }
}
