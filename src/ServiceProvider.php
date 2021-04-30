<?php

namespace ThinkOne\LaravelDuskReporter;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
            ]);
        }
    }

    public function register()
    {
        $this->app->bind(Reporter::class, function ($app) {
            if (! Reporter::$storeBuildAt) {
                Reporter::$storeBuildAt = storage_path('laravel-dusk-reporter');
            }

            return new Reporter();
        });
    }
}
