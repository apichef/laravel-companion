<?php

namespace Sarala;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    public function scopeFilter(Builder $query, FilterAbstract $builder)
    {
        return $builder->apply($query);
    }
}
