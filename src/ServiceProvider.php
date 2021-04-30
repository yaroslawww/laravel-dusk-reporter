<?php

namespace ThinkOne\LaravelDuskReporter;

use ThinkOne\LaravelDuskReporter\Commands\PurgeFilesCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                PurgeFilesCommand::class,
            ]);
        }
    }

    public function register()
    {
        Reporter::$stopReporting = (bool) getenv('DUSK_REPORT_DISABLED');
        $this->app->bind(Reporter::class, function ($app) {
            if (! Reporter::$storeBuildAt) {
                Reporter::$storeBuildAt = base_path(getenv('DUSK_REPORT_PATH') ?: 'storage/laravel-dusk-reporter');
            }

            return new Reporter();
        });
    }
}
