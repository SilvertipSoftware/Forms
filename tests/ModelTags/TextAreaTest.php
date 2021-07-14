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
        $this->options = [
            'object' => $this->post
        ];
    }

    public function testItMakesATextAreaTag()
    {
        $result = \Form::textAreaWithObject('post', 'content', $this->options);

        $this->assertStringContainsString('Content', $result);
        $this->assertStringContainsString('</textarea>', $result);
        $this->assertStringNotContainsString("\n", $result);
    }

    public function testItRendersNewlinesProperly()
    {
        $result = \Form::textAreaWithObject('post', 'content', "Multi\r\nline\n\rcontent\r", $this->options);

        $this->assertStringContainsString('Multi', $result);
        $this->assertStringContainsString('</textarea>', $result);
        $this->assertStringContainsString("&#10;", $result);

        $result = \Form::textAreaWithObject(
            'post',
            'content',
            "So
             is
             this",
             $this->options
        );
        $this->assertStringContainsString('So', $result);
        $this->assertStringContainsString('</textarea>', $result);
        $this->assertStringContainsString("&#10;", $result);
    }
}
