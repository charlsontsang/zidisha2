<?php

namespace Zidisha\Html;

use Illuminate\Support\ServiceProvider;

class BootstrapFormServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('bootstrap-form', function($app)
        {
            return new \Zidisha\Html\BootstrapFormBuilder(
                $app['html'],
                $app['form'],
                $app['config'],
                $app['session']
            );
        });
    }
    
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('bootstrap-form');
    }

}