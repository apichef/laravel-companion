<?php

namespace Sarala\Dummy\Transformers;

use League\Fractal\TransformerAbstract;
use Sarala\Dummy\User;

class UserTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['posts'];

    public function transform(User $user)
    {
        return [
            'id' => (int) $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    }

    public function includePosts(User $user)
    {
        return $this->collection($user->posts, new PostTransformer(), 'posts');
    }
}
