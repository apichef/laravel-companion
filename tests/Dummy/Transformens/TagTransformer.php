<?php

namespace Sarala\Dummy\Transformers;

use League\Fractal\TransformerAbstract;
use Sarala\Dummy\Tag;

class TagTransformer extends TransformerAbstract
{
    public function transform(Tag $tag)
    {
        return [
            'id' => (int) $tag->id,
            'name' => $tag->name,
        ];
    }
}