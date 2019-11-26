<?php

namespace SilvertipSoftware\Forms\Tests\ModelTags;

use Illuminate\Support\HtmlString;
use SilvertipSoftware\Forms\Tests\Post;
use SilvertipSoftware\Forms\Tests\TestCase;

class NumberFieldTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->post = new Post([
            'rating' => 6
        ]);
    }

    public function testItMakesANumberInputTag()
    {
        $result = \Form::numberFieldWithObject('post', 'rating');

        $this->assertStringContainsString('type="number"', $result);
    }

    public function testCanSetRange()
    {
        $result = \Form::numberFieldWithObject('post', 'rating', [
            'in' => [1,10]
        ]);

        $this->assertStringContainsString('min="1"', $result);
        $this->assertStringContainsString('max="10"', $result);
    }
}
