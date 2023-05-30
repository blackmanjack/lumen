<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('importdb', function ($app) {
            return new \App\Console\Commands\ImportSqlCommand;
        });
    
        $this->commands([
            'importdb',
        ]);
    }
}
