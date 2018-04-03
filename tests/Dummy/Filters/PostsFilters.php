<?php

namespace Sarala\Dummy\Filters;

use Sarala\FilterAbstract;

class PostsFilters extends FilterAbstract
{
    protected $lookup = [
        'my' => 'composedByMe'
    ];

    public function composedByMe()
    {
        return $this->builder->composedBy(auth()->user());
    }
}
