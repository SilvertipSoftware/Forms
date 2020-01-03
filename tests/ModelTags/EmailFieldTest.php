<?php

namespace SilvertipSoftware\Forms\Tests\ModelTags;

use SilvertipSoftware\Forms\Tests\Post;
use SilvertipSoftware\Forms\Tests\TestCase;

class EmailFieldTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->post = new Post([
            'email' => 'jon@doe.com'
        ]);
    }

    public function testItMakesAEmailInputTag()
    {
        $result = \Form::emailFieldWithObject('post', 'email');

        $this->assertStringContainsString('type="email"', $result);
    }
}
