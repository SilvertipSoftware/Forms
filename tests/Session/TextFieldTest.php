<?php

namespace SilvertipSoftware\Forms\Tests\Session;

use SilvertipSoftware\Forms\Tests\Post;
use SilvertipSoftware\Forms\Tests\TestCase;

class TextFieldTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();

        $this->post = new Post([
            'title' => 'First Post',
            'body' => 'The Body',
            'author_id' => 2
        ]);
    }

    public function testTextFieldWithOldInput()
    {
        $oldValue = 'test-old-value';
        app('router')->get('textfield', ['middleware' => 'web', 'uses' => function () use ($oldValue) {
            $request = request()->merge(['post' => ['title' => $oldValue]]);
            $request->flash();

            $options = [
                'object' => $this->post,
            ];

            return view('flash.text_field', ['options' => $options]);
        }]);

        $response = $this->call('GET', 'textfield');
        $this->assertEquals(200, $response->status());
        $this->assertStringContainsString("value=\"$oldValue\"", $response->getContent());
    }

    public function testTextFieldWithOldInputNestedName()
    {
        $oldValue = 'test-old-value';
        app('router')->get('textfield', ['middleware' => 'web', 'uses' => function () use ($oldValue) {
            $request = request()->merge(['post[some][nested][path]' => ['title' => $oldValue]]);
            $request->flash();

            $options = [
                'object' => $this->post,
            ];

            return view('flash.nested_text_field', ['options' => $options]);
        }]);

        $response = $this->call('GET', 'textfield');
        $this->assertEquals(200, $response->status());
        $this->assertStringContainsString("value=\"$oldValue\"", $response->getContent());
        $this->assertStringContainsString("post[some][nested][path]", $response->getContent());
    }
}
