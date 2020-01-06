<?php

namespace SilvertipSoftware\Forms\Tests\ModelTags;

use SilvertipSoftware\Forms\Tests\Post;
use SilvertipSoftware\Forms\Tests\TestCase;

class TextAreaTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->post = new Post([
            'content' => 'Content'
        ]);
    }

    public function testItMakesATextAreaTag()
    {
        $result = \Form::textAreaWithObject('post', 'content');

        $this->assertStringContainsString('</textarea>', $result);
    }
}
