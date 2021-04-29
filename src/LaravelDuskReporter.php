<?php


namespace ThinkOne\LaravelDuskReporter;

use Illuminate\Support\Facades\Facade;

class LaravelDuskReporter extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Reporter::class;
    }
}
