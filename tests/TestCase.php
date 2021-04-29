<?php

namespace ThinkOne\LaravelDuskReporter\Tests;

use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            \ThinkOne\LaravelDuskReporter\ServiceProvider::class,
        ];
    }

    public function defineEnvironment($app)
    {
    }
}
