<?php

namespace Nuwebs\PrimevueDatatable;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class PrimevueDatatableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->bind('primevue-datatable', function (Application $app) {
            return new PrimevueDatatable;
        });
        $this->app->alias('primevue-datatable', 'Nuwebs\PrimevueDatatable');
    }
}