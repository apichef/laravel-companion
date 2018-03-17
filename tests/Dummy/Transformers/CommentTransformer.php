<?php

namespace Sarala\Dummy\Transformers;

use League\Fractal\TransformerAbstract;
use Sarala\Dummy\Comment;

class CommentTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['author'];

    public function transform(Comment $comment)
    {
        return [
            'id' => (int) $comment->id,
            'body' => $comment->body,
            'created_at' => $comment->created_at->toDateString(),
        ];
    }

    public function includeAuthor(Comment $comment)
    {
        return $this->item($comment->author, new UserTransformer(), 'users');
    }
}