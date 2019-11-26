<?php

namespace SilvertipSoftware\Forms\Tests\ModelTags;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\HtmlString;
use SilvertipSoftware\Forms\Tests\Author;
use SilvertipSoftware\Forms\Tests\Post;
use SilvertipSoftware\Forms\Tests\TestCase;

class CollectionSelectFieldTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->post = new Post([
            'author_id' => 2
        ]);
        $this->allAuthors = new Collection([
            new Author(['id' => 1, 'name' => 'Dante']),
            new Author(['id' => 2, 'name' => 'Hypatia'])
        ]);
        $this->options = [
            'object' => $this->post
        ];
    }

    public function testItMakesAnHtmlString()
    {
        $result = \Form::collectionSelectWithObject('post', 'author_id', $this->allAuthors, 'id', 'name', $this->options);

        $this->assertInstanceOf(HtmlString::class, $result);
    }

    public function testItMakesASelectTag()
    {
        $result = \Form::collectionSelectWithObject('post', 'author_id', $this->allAuthors, 'id', 'name', $this->options);

        $this->assertSeeTag('select', $result);
        $this->assertStringContainsString('</select>', $result);
    }

    public function testItDefaultsNameAndId()
    {
        $result = \Form::collectionSelectWithObject('post', 'author_id', $this->allAuthors, 'id', 'name', $this->options);

        $this->assertStringContainsString('name="post[author_id]"', $result);
        $this->assertStringContainsString('id="post_author_id"', $result);
    }

    public function testItIncludesOptions()
    {
        $result = \Form::collectionSelectWithObject('post', 'author_id', $this->allAuthors, 'id', 'name', $this->options);

        $this->assertStringContainsString('>Dante</option>', $result);
        $this->assertStringContainsString('value="1"', $result);
        $this->assertStringContainsString('>Hypatia</option>', $result);
        $this->assertStringContainsString('value="2"', $result);
    }

    public function testItEscapesOptions()
    {
        $this->allAuthors->get(0)->name = 'Da>nte';
        $result = \Form::collectionSelectWithObject('post', 'author_id', $this->allAuthors, 'name', 'name', $this->options);

        $this->assertStringContainsString('value="Da&gt;nte"', $result);
        $this->assertStringContainsString('>Da&gt;nte</option>', $result);
    }

    public function testItCanUseMethodForOptionString()
    {
        $result = \Form::collectionSelectWithObject('post', 'author_id', $this->allAuthors, 'id', 'getTitledName', $this->options);

        $this->assertStringContainsString('>Dr. Hypatia</option>', $result);
    }

    public function testItCanAddBlankOption()
    {
        $options = array_merge($this->options, [
            'include_blank' => 'Select author'
        ]);
        $result = \Form::collectionSelectWithObject('post', 'author_id', $this->allAuthors, 'id', 'name', $options);

        $this->assertStringContainsString('>Select author</option>', $result);
        $this->assertStringContainsString('value=""', $result);
    }

    public function testItCanAddPromptWhenNoneSelected()
    {
        $this->post->author_id = null;
        $options = array_merge($this->options, [
            'prompt' => 'Make selection'
        ]);
        $result = \Form::collectionSelectWithObject('post', 'author_id', $this->allAuthors, 'id', 'name', $options);

        $this->assertStringContainsString('>Make selection</option>', $result);
        $this->assertStringContainsString('value=""', $result);
    }

    public function testItOmitsPromptWhenSomeSelected()
    {
        $options = array_merge($this->options, [
            'prompt' => 'Make selection'
        ]);
        $result = \Form::collectionSelectWithObject('post', 'author_id', $this->allAuthors, 'id', 'name', $options);

        $this->assertStringNotContainsString('>Make selection</option>', $result);
        $this->assertStringNotContainsString('value=""', $result);
    }

    public function testCanExplicitlySelectOption()
    {
        $options = array_merge($this->options, [
            'selected' => 1
        ]);
        $result = \Form::collectionSelectWithObject('post', 'author_id', $this->allAuthors, 'id', 'name', $options);

        $this->assertStringContainsString('<option selected="selected" value="1">', $result);
        $this->assertStringNotContainsString('<option selected="selected" value="2">', $result);
    }

    public function testCanSelectMultipleOptions()
    {
        $this->post->author_id = null;
        $options = array_merge($this->options, [
            'selected' => [1, 2]
        ]);
        $result = \Form::collectionSelectWithObject('post', 'author_id', $this->allAuthors, 'id', 'name', $options);

        $this->assertStringContainsString('<option selected="selected" value="1">', $result);
        $this->assertStringContainsString('<option selected="selected" value="2">', $result);
    }

    public function testCanDisableSpecificOptions()
    {
        $options = array_merge($this->options, [
            'disabled' => 1
        ]);
        $result = \Form::collectionSelectWithObject('post', 'author_id', $this->allAuthors, 'id', 'name', $options);

        $this->assertStringContainsString('<option disabled="disabled" value="1">', $result);
    }

    public function testCanDisableMultipleOptions()
    {
        $this->post->author_id = null;
        $options = array_merge($this->options, [
            'disabled' => [1, 2]
        ]);
        $result = \Form::collectionSelectWithObject('post', 'author_id', $this->allAuthors, 'id', 'name', $options);

        $this->assertStringContainsString('<option disabled="disabled" value="1">', $result);
        $this->assertStringContainsString('<option disabled="disabled" value="2">', $result);
    }

    public function testCanPassHtmlAttributes()
    {
        $htmlOptions = [
            'class' => 'red'
        ];
        $result = \Form::collectionSelectWithObject('post', 'author_id', $this->allAuthors, 'id', 'name', $this->options, $htmlOptions);

        $this->assertStringContainsString('class="red"', $result);
    }

    public function testItIncludesBackingHiddenInputForMultiples()
    {
        $htmlOptions = [
            'multiple' => true
        ];
        $result = \Form::collectionSelectWithObject('post', 'author_id', $this->allAuthors, 'id', 'name', $this->options, $htmlOptions);

        $this->assertSeeTag('input', $result);
        $this->assertStringContainsString('type="hidden"', $result);        
    }
}