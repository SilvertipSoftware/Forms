<?php

namespace SilvertipSoftware\Forms\Tests\Session;

use SilvertipSoftware\Forms\Tests\Address;
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
            'author_id' => 2,
            'address' => new Address([
                'city' => 'Vancouver',
                'country' => 'CA',
                'company' => 'Acme Inc.',
                'address1' => '123 Main st',
                'phone' => '555-5555',
            ])
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
        app('router')->get('textfield', ['middleware' => 'web', 'uses' => function () {
            $request = request()->merge([
                'post[address][company]' => 'New Company',
                'post[address][country]' => 'US',
                'post[address][phone]' => '867-5309',
            ]);
            $request->flash();

            $options = [
                'object' => $this->post,
            ];

            return view('flash.nested_text_field', ['options' => $options]);
        }]);

        $response = $this->call('GET', 'textfield');
        $this->assertEquals(200, $response->status());
        $this->assertStringContainsString("value=\"US\" name=\"post[address][country]\"", $response->getContent());
        $this->assertStringContainsString("value=\"New Company\" name=\"post[address][company]\"", $response->getContent());
        $this->assertStringContainsString("value=\"867-5309\" name=\"post[address][phone]\"", $response->getContent());
    }
}
