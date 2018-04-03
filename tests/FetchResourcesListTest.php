<?php

namespace Sarala;

use Illuminate\Http\Request;
use Sarala\Dummy\Filters\PostsFilters;
use Sarala\Dummy\Post;
use Sarala\Dummy\Transformers\PostTransformer;

class FetchResourcesListTest extends TestCase
{
    public function test_all()
    {
        factory(Post::class, 15)->create();

        $request = Request::create('');

        $filters = new PostsFilters($request);
        $data = Post::filter($filters)->paginateOrGet($request);
        $response = new JsonApiResponse($data, new PostTransformer(), 'posts');
        $response = $this->getTestJsonApiResponse($response, $request);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
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
            ]
        ]);

        $data = $this->getContentFromResponse($response);

        $this->assertCount(15, $data['data']);
    }

    public function test_pagination()
    {
        factory(Post::class, 15)->create();

        $request = Request::create('', 'GET', [
            'page' => [
                'number' => 2,
                'size' => 4,
            ]
        ]);

        $filters = new PostsFilters($request);
        $data = Post::filter($filters)->paginateOrGet($request);
        $response = new JsonApiResponse($data, new PostTransformer(), 'posts');
        $response = $this->getTestJsonApiResponse($response, $request);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
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
            ],
            'links' => [
                'self',
                'first',
                'prev',
                'next',
                'last',
            ]
        ])->assertJsonFragment([
            'meta' => [
                'pagination' => [
                    'total' => 15,
                    'count' => 4,
                    'per_page' => 4,
                    'current_page' => 2,
                    'total_pages' => 4,
                ]
            ]
        ]);
    }
}
