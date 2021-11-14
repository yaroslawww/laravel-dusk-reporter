<?php

namespace LaravelDuskReporter;

use Closure;
use Illuminate\Support\Str;
use LaravelDuskReporter\Generation\ReportFile;
use LaravelDuskReporter\Generation\ReportFileContract;
use LaravelDuskReporter\Generation\ReportScreenshot;
use LaravelDuskReporter\Generation\ReportScreenshotContract;

class Reporter
{
    /**
     * Report files extension.
     *
     * @var string
     */
    public static string $fileExt = 'md';

    /**
     * Index file base name.
     *
     * @var string
     */
    public static string $indexFileBaseName = 'index';

    /**
     * The directory that will contain any build files.
     *
     * @var string
     */
    public static string $storeBuildAt = '';

    /**
     * The directory that will contain any screenshots.
     * If null or empty than will be used "$storeBuildAt" field.
     *
     * @var ?string
     */
    public static ?string $storeScreenshotAt = null;

    /**
     * Use relative path to screenshot.
     *
     * @var bool
     */
    public static bool $screenshotRelativePath = true;

    /**
     * Disable screenshots making.
     *
     * @var bool
     */
    public static bool $disableScreenshots = false;

    /**
     * Fully disable reporting.
     *
     * @var bool
     */
    public static bool $stopReporting = false;

    /**
     * Closure tio find body element.
     *
     * @var Closure|null
     */
    public static ?Closure $getBodyElementCallback = null;

    /**
     * Check is report file name valid, and if not - amend it.
     *
     * @param string $name
     *
     * @return string
     */
    public static function getValidFileName(string $name): string
    {
        $fileExt = static::$fileExt;
        if (empty($name)) {
            $name = Str::random();
        }
        if (Str::endsWith($name, ".{$fileExt}")) {
            $name .= ".{$fileExt}";
        }

        return $name;
    }

    /**
     * Get new report file.
     *
     * @param string $name
     *
     * @return ReportFileContract
     */
    public function newFile(string $name): ReportFileContract
    {
        return new ReportFile($this, $name);
    }

    /**
     * Get new screenshot manager.
     *
     * @return ReportScreenshotContract
     */
    public function screenshoter(): ReportScreenshotContract
    {
        return new ReportScreenshot($this);
    }

    /**
     * @param Closure|null $getBodyElementCallback
     *
     * @return $this
     */
    public function setBodyElementSearchCallback(?Closure $getBodyElementCallback): self
    {
        static::$getBodyElementCallback = $getBodyElementCallback;

        return $this;
    }

    /**
     * Get store build folder
     *
     * @param string $path - File path, optional
     *
     * @return string
     */
    public function storeBuildAt(string $path = ''): string
    {
        return rtrim(static::$storeBuildAt, '/') . ($path ? ('/' . ltrim($path, '/')) : '');
    }

    /**
     * Get store screenshots folder
     *
     * @param string $path - File path, optional
     *
     * @return string
     */
    public function storeScreenshotAt(string $path = ''): string
    {
        $screenshotDir = static::$storeScreenshotAt;
        if (!$screenshotDir) {
            $screenshotDir = $this->storeBuildAt();
        }

        return rtrim($screenshotDir, '/') . ($path ? ('/' . ltrim($path, '/')) : '');
    }

    /**
     * Check if need use relative path
     *
     * @return bool
     */
    public function useScreenshotRelativePath(): bool
    {
        return static::$screenshotRelativePath && ($this->storeScreenshotAt() == $this->storeBuildAt());
    }

    /**
     * Check is screenshots disabled
     *
     * @return bool
     */
    public function isScreenshotsDisabled(): bool
    {
        return static::$disableScreenshots;
    }

    /**
     * Check is reporting disabled
     *
     * @return bool
     */
    public function isReportingDisabled(): bool
    {
        return static::$stopReporting;
    }

    /**
     * Add file to table of contents.
     *
     * @param string $name
     *
     * @return void
     */
    public function addToTableOfContents(string $name): void
    {
        if (!$this->isReportingDisabled()) {
            $reportFile = $this->reportFileName($name, true);

            $filePath = static::getValidFileName("{$this->storeBuildAt()}/" . static::$indexFileBaseName);

            $directoryPath = dirname($filePath);

            if (!is_dir($directoryPath)) {
                mkdir($directoryPath, 0777, true);
            }

            if (!file_exists($filePath)) {
                file_put_contents($filePath, '# ' . trans('dusk-reporter::report.table_of_contents') . PHP_EOL . PHP_EOL);
            }

            if (!str_contains(file_get_contents($filePath), $reportFile)) {
                file_put_contents($filePath, "- [{$reportFile}]({$reportFile})" . PHP_EOL, FILE_APPEND | LOCK_EX);
            }
        }
    }

    /**
     * Get report file name
     *
     * @param string $name
     * @param bool $relative
     *
     * @return string
     */
    public function reportFileName(string $name, bool $relative = false): string
    {
        return ($relative ? '' : "{$this->storeBuildAt()}/") . static::getValidFileName($name);
    }
}
