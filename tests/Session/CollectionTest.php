<?php

namespace SilvertipSoftware\Forms\Tests\Session;

use Illuminate\Support\Collection;
use SilvertipSoftware\Forms\Tests\Post;
use SilvertipSoftware\Forms\Tests\Author;
use SilvertipSoftware\Forms\Tests\TestCase;

class CollectionTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();

        $this->post = new Post([
            'title' => 'First Post',
            'body' => 'The Body',
            'author_id' => 2
        ]);
        
        $this->allAuthors = new Collection([
            new Author(['id' => 1, 'name' => 'Dante']),
            new Author(['id' => 2, 'name' => 'Hypatia']),
            new Author(['id' => 3, 'name' => 'John'])
        ]);
    }

    public function testCollectionWithOldInput()
    {
        $oldAuthorId = '1';
        app('router')->get('collection', ['middleware' => 'web', 'uses' => function () use ($oldAuthorId) {
            $request = request()->merge(['post' => ['author_id' => $oldAuthorId]]);
            $request->flash();

            $options = [
                'object' => $this->post,
                'selected' => 3
            ];

            return view('flash.collection', ['authors' => $this->allAuthors, 'options' => $options]);
        }]);

        $response = $this->call('GET', 'collection');
        $this->assertEquals(200, $response->status());
        $this->assertStringContainsString("selected=\"selected\" value=\"$oldAuthorId\"", $response->getContent());
    }

    public function testCollectionWithOldInputNestedName()
    {
        $oldAuthorId = '1';
        app('router')->get('collection', ['middleware' => 'web', 'uses' => function () use ($oldAuthorId) {
            $request = request()->merge(['post[some][nested][path]' => ['author_id' => $oldAuthorId]]);
            $request->flash();

            $options = [
                'object' => $this->post,
                'selected' => 3
            ];

            return view('flash.nested_collection', ['authors' => $this->allAuthors, 'options' => $options]);
        }]);

        $response = $this->call('GET', 'collection');
        $this->assertEquals(200, $response->status());
        $this->assertStringContainsString("selected=\"selected\" value=\"$oldAuthorId\"", $response->getContent());
        $this->assertStringContainsString("post[some][nested][path]", $response->getContent());
    }

}
