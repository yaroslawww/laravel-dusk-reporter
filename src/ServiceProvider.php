<?php

namespace LaravelDuskReporter;

use LaravelDuskReporter\Commands\PurgeFilesCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                PurgeFilesCommand::class,
            ]);
            $this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/dusk-reporter'),
            ]);
        }

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'dusk-reporter');
    }

    public function register()
    {
        Reporter::$stopReporting      = (bool) getenv('DUSK_REPORT_DISABLED');
        Reporter::$disableScreenshots = (bool) getenv('DUSK_SCREENSHOTS_DISABLED');
        $this->app->bind(Reporter::class, function () {
            if (!Reporter::$storeBuildAt) {
                Reporter::$storeBuildAt = base_path(getenv('DUSK_REPORT_PATH') ?: 'storage/laravel-dusk-reporter');
            }

            return new Reporter();
        });
    }
}
