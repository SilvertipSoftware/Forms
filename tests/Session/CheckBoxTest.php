<?php

namespace SilvertipSoftware\Forms\Tests\Session;

use SilvertipSoftware\Forms\Tests\Post;
use SilvertipSoftware\Forms\Tests\TestCase;

class CheckBoxTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();

        $this->post = new Post([
            'title' => 'First Post',
            'body' => 'The Body',
            'is_published' => true,
        ]);
    }

    public function testCheckBoxWithOldInput()
    {
        $oldValue = false;
        app('router')->get('checkbox', ['middleware' => 'web', 'uses' => function () use ($oldValue) {
            $request = request()->merge([
                'post' => [
                    'is_published' => $oldValue
                ]
            ]);
            $request->flash();

            $options = [
                'object' => $this->post,
            ];

            return view('flash.checkbox', ['options' => $options]);
        }]);

        $response = $this->call('GET', 'checkbox');
        $this->assertEquals(200, $response->status());
        $this->assertStringNotContainsString("checked=\"checked\"", $response->getContent());
    }


    public function testCheckBoxWithOldInputNestedName()
    {
        $oldValue = false;
        app('router')->get('checkbox', ['middleware' => 'web', 'uses' => function () use ($oldValue) {
            $request = request()->merge([
                'post' => [
                    'some' => [
                        'nested' => [
                            'path' => [
                                'is_published' => $oldValue
                            ]
                        ]
                    ]
                ]
            ]);
            $request->flash();

            $options = [
                'object' => $this->post,
            ];

            return view('flash.nested_checkbox', ['options' => $options]);
        }]);

        $response = $this->call('GET', 'checkbox');
        $this->assertEquals(200, $response->status());
        $this->assertStringNotContainsString("checked=\"checked\"", $response->getContent());
        $this->assertStringContainsString("post[some][nested][path]", $response->getContent());
    }
}
