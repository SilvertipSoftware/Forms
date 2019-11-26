<?php

namespace SilvertipSoftware\Forms\Tests\ModelTags;

use Illuminate\Support\HtmlString;
use SilvertipSoftware\Forms\Tests\Post;
use SilvertipSoftware\Forms\Tests\TestCase;

class LabelTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->post = new Post([
            'title' => 'First Post'
        ]);
        $this->options = [
            'object' => $this->post
        ];
    }

    public function testItMakesAnHtmlString()
    {
        $result = \Form::labelWithObject('post', 'title', $this->options);

        $this->assertInstanceOf(HtmlString::class, $result);
    }

    public function testItMakesALabelTag()
    {
        $result = \Form::labelWithObject('post', 'title', $this->options);

        $this->assertSeeTag('label', $result);
        $this->assertStringContainsString('</label>', $result);
    }

    public function testItSetsForAttribute()
    {
        $result = \Form::labelWithObject('post', 'title', $this->options);

        $this->assertStringContainsString('for="post_title"', $result);
    }

    public function testItDoesNotName()
    {
        $result = \Form::labelWithObject('post', 'title', $this->options);

        $this->assertStringNotContainsString('name="', $result);
    }

    public function testItProvidesDefaultContent()
    {
        $result = \Form::labelWithObject('post', 'title', $this->options);

        $this->assertStringContainsString('>Title</label>', $result);
    }

    public function testItMassagesAttributeName()
    {
        $result = \Form::labelWithObject('post', 'full_title', $this->options);

        $this->assertStringContainsString('>Full Title</label>', $result);
    }

    public function testItAutoTranslatesContent()
    {
        app('translator')->addLines([
            'posts.title' => 'Titre'
        ], 'en');
        $result = \Form::labelWithObject('post', 'title', $this->options);

        $this->assertStringContainsString('>Titre</label>', $result);
    }

    public function testCanSupplyContent()
    {
        $result = \Form::labelWithObject('post', 'title', 'Name of Post', $this->options);

        $this->assertStringContainsString('>Name of Post</label>', $result);
    }
}
