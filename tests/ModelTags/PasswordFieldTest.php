<?php

namespace SilvertipSoftware\Forms\Tests\ModelTags;

use Illuminate\Support\HtmlString;
use SilvertipSoftware\Forms\Tests\Post;
use SilvertipSoftware\Forms\Tests\TestCase;

class PasswordFieldTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->post = new Post([
            'secret' => 'top_secret'
        ]);
    }

    public function testItMakesAPasswordInputTag()
    {
        $result = \Form::passwordFieldWithObject('post', 'secret');

        $this->assertStringContainsString('type="password"', $result);
    }
}
