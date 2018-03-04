<?php

namespace MilroyFraser\JsonApiCompanions\Query;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class JsonApiQueryBuilder
{
    /** @var Builder $builder */
    protected $builder;

    /** @var Request $request */
    protected $request;

    protected $lookup = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        $this->applyIncludes();
        $this->applyFilters();
        $this->applySorts();

        return $this->builder;
    }

    private function applyIncludes()
    {
        if ($this->request->filled('include')) {
            $includes = explode(',', $this->request->get('include'));
            $this->builder->with($includes);
        }
    }

    private function applyFilters()
    {
        foreach ($this->getFilters() as $name => $value) {
            if (isset($this->lookup[$name]) && method_exists($this, $this->lookup[$name])) {
                $name = $this->lookup[$name];

                if (strlen($value)) {
                    $this->$name($value);
                } else {
                    $this->$name();
                }
            }
        }
    }

    private function applySorts()
    {
        if ($this->request->filled('sort')) {
            $sortBy = explode(',', $this->request->get('sort'));

            foreach ($sortBy as $field) {
                if (starts_with($field, '-')) {
                    $this->builder->orderByDesc(str_after($field, '-'));
                } else {
                    $this->builder->orderBy($field);
                }
            }
        }
    }

    private function getFilters(): array
    {
        return $this->request->get('filter', []);
    }
}
