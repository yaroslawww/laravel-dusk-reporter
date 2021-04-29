<?php


namespace ThinkOne\LaravelDuskReporter;

use Closure;
use ThinkOne\LaravelDuskReporter\Generation\ReportFile;
use ThinkOne\LaravelDuskReporter\Generation\ReportFileContract;
use ThinkOne\LaravelDuskReporter\Generation\ReportScreenshot;

class Reporter
{

    /**
     * The directory that will contain any build files.
     *
     * @var string
     */
    public static $storeBuildAt = '';

    /**
     * The directory that will contain any screenshots.
     *
     * @var string
     */
    public static $storeScreenshotAt = '';

    /**
     * Closure tio find body element.
     *
     * @var Closure|null
     */
    public static ?Closure $getBodyElementCallback = null;

    public function newFile(string $name): ReportFileContract
    {
        return new ReportFile($this, $name);
    }

    public function screenshoter(): ReportScreenshot
    {
        return new ReportScreenshot($this);
    }

    public function setBodyElementSearchCallback(?Closure $getBodyElementCallback): self
    {
        static::$getBodyElementCallback = $getBodyElementCallback;

        return $this;
    }
}
