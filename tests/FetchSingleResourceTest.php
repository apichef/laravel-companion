<?php

namespace Sarala;

use Illuminate\Http\Request;
use Sarala\Dummy\Filters\PostsFilters;
use Sarala\Dummy\Post;
use Sarala\Dummy\Transformers\PostTransformer;

class FetchSingleResourceTest extends TestCase
{
    public function test_it_response_with_link_and_data()
    {
        $post = factory(Post::class)->create();

        $request = Request::create('');
        $filters = new PostsFilters($request);
        $data = Post::filter($filters)->find($post->id);
        $response = new JsonApiResponse($data, new PostTransformer(), 'posts');

        $this->getTestJsonApiResponse($response, $request)->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'title',
                    'subtitle',
                    'body',
                    'published_at',
                ],
                'links',
            ]
        ]);
    }
}