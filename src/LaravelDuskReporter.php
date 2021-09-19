<?php


namespace LaravelDuskReporter;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string storeBuildAt(string $path = '')
 * @method static \LaravelDuskReporter\Generation\ReportFileContract newFile(string $name)
 */
class LaravelDuskReporter extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Reporter::class;
    }
}
