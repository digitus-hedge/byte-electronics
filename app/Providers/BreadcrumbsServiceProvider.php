<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class BreadcrumbsServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Inertia::share('breadcrumbs', function () {
            $segments = Request::segments();

            return array_map(fn($segment) => ucwords(str_replace('-', ' ', $segment)), $segments);
        });
    }
}
