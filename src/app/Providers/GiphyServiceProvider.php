<?php

namespace App\Providers;

use App\Services\GiphyService;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class GiphyServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('GiphyService', function (Application $app) {
            return new GiphyService();
        });
    }

    /**
     * Boot the services provided by the provider.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
