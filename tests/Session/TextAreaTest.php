<?php

namespace SilvertipSoftware\Forms\Tests\Session;

use SilvertipSoftware\Forms\Tests\Post;
use SilvertipSoftware\Forms\Tests\Comment;
use SilvertipSoftware\Forms\Tests\TestCase;

class TextAreaTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();

        $this->post = new Post([
            'title' => 'First Post',
            'body' => 'The Body',
            'comments' => [
                new Comment(['id' => 998, 'body' => 'First Comment']),
                new Comment(['id' => 999, 'body' => 'Second Comment']),
                new Comment(['id' => 1000, 'body' => 'Third Comment']),
            ],
            'author_id' => 2
        ]);
    }

    public function testTextAreaWithOldInput()
    {
        $oldValue = 'test-old-value';
        app('router')->get('textarea', ['middleware' => 'web', 'uses' => function () use ($oldValue) {
            $request = request()->merge(['post' => ['body' => $oldValue]]);
            $request->flash();

            $options = [
                'object' => $this->post,
            ];

            return view('flash.text_area', ['options' => $options]);
        }]);

        $response = $this->call('GET', 'textarea');
        $this->assertEquals(200, $response->status());
        $this->assertStringContainsString("$oldValue</textarea>", $response->getContent());
    }

    public function testTextAreaWithOldInputNestedObject()
    {
        $oldValue = 'test-old-value';
        app('router')->get('textarea', ['middleware' => 'web', 'uses' => function () use ($oldValue) {
            $request = request()->merge(["post.comments.1.body" => $oldValue]);
            $request->flash();

            $options = [
                'object' => $this->post,
            ];

            return view('flash.nested_text_area', ['options' => $options]);
        }]);

        $response = $this->call('GET', 'textarea');
        $this->assertEquals(200, $response->status());
        $this->assertStringContainsString("$oldValue</textarea>", $response->getContent());
        $this->assertStringContainsString("post[comments][0][body]", $response->getContent());
        $this->assertStringContainsString("post[comments][1][body]", $response->getContent());
        $this->assertStringContainsString("post[comments][2][body]", $response->getContent());
    }
}
