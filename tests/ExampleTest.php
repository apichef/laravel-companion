<?php

namespace Sarala;

use Illuminate\Http\Request;
use Sarala\Dummy\Comment;
use Sarala\Dummy\Filters\PostsFilter;
use Sarala\Dummy\Post;
use Sarala\Dummy\Tag;
use Sarala\Dummy\Transformers\PostTransformer;
use Sarala\Dummy\User;

class ExampleTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->seedDB();
    }

    public function test_it_works()
    {
        $params = [
            "include" => "author,tags,comments.author",
            "sort" => "-published_at",
            "page" => [
                "size" => "4",
                "number" => "1",
            ],
        ];

        $request = Request::create('', 'GET', $params);
        $query = new PostsFilter($request);
        $data = Post::filter($query)->paginateOrGet($request);
        $response = (new JsonApiResponse($data, new PostTransformer(), 'posts'))
            ->toResponse($request)
            ->original;

        $this->assertEquals([
            'data',
            'included',
            'meta',
            'links'
        ], array_keys($response));

        $this->assertEquals(4, count($response['data']));
    }

    private function seedDB()
    {
        factory(Tag::class, 20)->create();

        factory(User::class, 10)->create()->each(function (User $user) {
            $post = factory(Post::class)->create(['author_id' => $user->id]);
            factory(Comment::class, rand(1, 5))->create(['post_id' => $post->id]);
            $post->tags()->attach(Tag::all()->random(rand(1, 5))->pluck('id')->all());
        });
    }
}
