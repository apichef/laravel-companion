<?php

namespace MilroyFraser\JsonApiCompanion\Query;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use MilroyFraser\JsonApiCompanions\Query\JsonApiQueryBuilder;

trait InteractsWithJsonApiQuery
{
    public function scopeApply(Builder $query, JsonApiQueryBuilder $builder)
    {
        return $builder->apply($query);
    }

    public static function paginateOrGet(JsonApiQueryBuilder $builder, Request $request)
    {
        $query = self::apply($builder);

        if ($request->filled('page')) {
            return $query->paginate($request->input('page.size'), null, null, $request->input('page.number'));
        }

        return $query->get();
    }
}
