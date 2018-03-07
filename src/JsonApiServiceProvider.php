<?php

namespace Sarala;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class JsonApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Builder::macro('paginateOrGet', function (Request $request) {
            if ($request->filled('page')) {
                return $this->paginate($request->input('page.size'), null, null, $request->input('page.number'));
            }

            return $this->get();
        });
    }
}
