<?php

namespace SilvertipSoftware\Forms\Tests\RawTags;

use Illuminate\Support\HtmlString;

trait GenericTagTestHelpers
{

    public function testItMakesAnHtmlString()
    {
        $result = $this->makeTagWithOptions([]);

        $this->assertInstanceOf(HtmlString::class, $result);
    }

    public function testItMakesDataAttrs()
    {
        $options = [
            'data' => [
                'option' => 123
            ]
        ];
        $result = $this->makeTagWithOptions($options);

        $this->assertStringContainsString('data-option="123"', $result);
    }

    public function testItMakesAriaAttrs()
    {
        $options = [
            'aria' => [
                'role' => 'button'
            ]
        ];
        $result = $this->makeTagWithOptions($options);

        $this->assertStringContainsString('aria-role="button"', $result);
    }

    public function testItMakesVueAttrs()
    {
        $options = [
            'v' => [
                'for' => 'i in list',
                'bind:id' => 'var'
            ]
        ];
        $result = $this->makeTagWithOptions($options);

        $this->assertStringContainsString('v-for="i in list"', $result);
        $this->assertStringContainsString('v-bind:id="var"', $result);
    }

    public function testItTreatsBooleanAttributesSpecially()
    {
        // not exhaustive...
        $options = [
            'autofocus' => 1,
            'checked' => 1,
            'readonly' => 1,
            'disabled' => 1,
            'visible' => 0
        ];
        $result = $this->makeTagWithOptions($options);

        foreach ($options as $key => $value) {
            if ($value) {
                $this->assertStringContainsString($key . '="' . $key . '"', $result);
            }
        }
    }

    public function testItConcatsArrayAttributes()
    {
        $options = [
            'class' => ['red', 'blue']
        ];
        $result = $this->makeTagWithOptions($options);

        $this->assertStringContainsString('class="red blue"', $result);
    }

    public function testItEscapesAttributes()
    {
        $options = [
            'class' => ['red', 'bl>ue'],
            'other' => 'quo"te'
        ];
        $result = $this->makeTagWithOptions($options);

        $this->assertStringContainsString('class="red bl&gt;ue"', $result);
        $this->assertStringContainsString('other="quo&quot;te"', $result);
    }
}