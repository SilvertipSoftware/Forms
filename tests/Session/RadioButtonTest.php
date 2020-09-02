<?php

namespace SilvertipSoftware\Forms\Tests\Session;

use SilvertipSoftware\Forms\Tests\Post;
use SilvertipSoftware\Forms\Tests\TestCase;

class RadioButtonTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();

        $this->post = new Post([
            'title' => 'First Post',
            'body' => 'The Body',
            'state' => 1,
        ]);
    }

    public function testRadioButtonWithOldInput()
    {
        $oldValue = 2;
        app('router')->get('radiobutton', ['middleware' => 'web', 'uses' => function () use ($oldValue) {
            $request = request()->merge([
                'post' => [
                    'state' => $oldValue
                ]
            ]);
            $request->flash();

            $options = [
                'object' => $this->post,
            ];

            return view('flash.radiobutton', ['options' => $options]);
        }]);

        $response = $this->call('GET', 'radiobutton');
        $this->assertEquals(200, $response->status());
        $this->assertStringNotContainsString("checked=\"checked\"", $response->getContent());
    }


    public function testRadioButtonWithOldInputNestedName()
    {
        $oldValue = 2;
        app('router')->get('radiobutton', ['middleware' => 'web', 'uses' => function () use ($oldValue) {
            $request = request()->merge([
                'post' => [
                    'some' => [
                        'nested' => [
                            'path' => [
                                'state' => $oldValue
                            ]
                        ]
                    ]
                ]
            ]);
            $request->flash();

            $options = [
                'object' => $this->post,
            ];

            return view('flash.nested_radiobutton', ['options' => $options]);
        }]);

        $response = $this->call('GET', 'radiobutton');
        $this->assertEquals(200, $response->status());
        $this->assertStringNotContainsString("checked=\"checked\"", $response->getContent());
        $this->assertStringContainsString("post[some][nested][path]", $response->getContent());
    }
}
