<?php

namespace SilvertipSoftware\Forms\Tests\ModelTags;

use SilvertipSoftware\Forms\Tests\Post;
use SilvertipSoftware\Forms\Tests\TestCase;

class DateFieldTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->post = new Post([
            'occurred_on' => '2021-05-01'
        ]);
    }

    public function testItMakesADateInputTag()
    {
        $result = \Form::dateFieldWithObject('post', 'occurred_on');

        $this->assertStringContainsString('type="date"', $result);
    }

    public function testCanSetRange()
    {
        $result = \Form::dateFieldWithObject('post', 'occurred_on', [
            'in' => ['2000-01-01', '2050-12-31']
        ]);

        $this->assertStringContainsString('min="2000-01-01"', $result);
        $this->assertStringContainsString('max="2050-12-31"', $result);
    }
}
