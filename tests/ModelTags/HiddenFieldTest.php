<?php

namespace SilvertipSoftware\Forms\Tests\ModelTags;

use SilvertipSoftware\Forms\Tests\Post;
use SilvertipSoftware\Forms\Tests\TestCase;

class HiddenFieldTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->post = new Post([
            'uuid' => '2fca9b59-c18a-4ee7-b3a4-42e510c0d510'
        ]);
    }

    public function testItMakesAHiddenInputTag()
    {
        $result = \Form::hiddenFieldWithObject('post', 'uuid');

        $this->assertStringContainsString('type="hidden"', $result);
    }
}
