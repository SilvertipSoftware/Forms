<?php

namespace SilvertipSoftware\Forms\Tests\RawTags;

use Illuminate\Support\HtmlString;
use SilvertipSoftware\Forms\Tests\TestCase;

class FormTagDefaultsTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->result = \Form::formTag('/abc');
    }

    public function testItMakesAnHtmlString()
    {
        $this->assertInstanceOf(HtmlString::class, $this->result);
    }

    public function testItMakesAFormTag()
    {
        $this->assertSeeTag('form', $this->result);
        $this->assertStringContainsString('method="post"', $this->result);
        $this->assertStringContainsString('action="/abc"', $this->result);
    }

    public function testItMakesARemoteForm()
    {
        $this->assertStringContainsString('data-remote="1"', $this->result);
    }

    public function testItIncludesUtf8Marker()
    {
        $this->assertStringContainsString('type="hidden" name="utf8"', $this->result);
    }

    public function testItIncludesCsrfToken()
    {
        app('session')->start();
        $this->result = \Form::formTag('/abc');
        $this->assertStringContainsString('type="hidden" name="_token"', $this->result);
        $this->assertStringContainsString('"' . csrf_token() . '"', $this->result);
    }
}