<?php


namespace ThinkOne\LaravelDuskReporter\Generation;

use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Support\Str;
use Imagick;
use Laravel\Dusk\Browser;
use ThinkOne\LaravelDuskReporter\Reporter;

class ReportScreenshot implements ReportScreenshotContract
{
    protected Reporter $reporter;

    protected string $fileExt = 'png';

    /**
     * ReportScreenshot constructor.
     *
     * @param Reporter $reporter
     */
    public function __construct(Reporter $reporter)
    {
        $this->reporter = $reporter;
    }

    /**
     * @inheritDoc
     */
    public function make(Browser $browser, string $filename, ?string $resize = null, ?string $suffix = null): string
    {
        $realFileName = "{$filename}.{$this->fileExt}";

        if (! $this->reporter->isReportingDisabled()) {
            $resize = is_string($resize) ? $resize : static::RESIZE_FIT;

            $defaultStoreScreenshotsAt = $browser::$storeScreenshotsAt;

            $browser::$storeScreenshotsAt = $this->reporter->storeScreenshotAt();

            $filename = $this->fileName($browser, $filename, $suffix);

            $realFileName = "{$filename}.{$this->fileExt}";

            if ($resize == static::RESIZE_COMBINE) {
                $this->reportScreenCombined($browser, $filename);
            } else {
                $defaultSize = $browser->driver->manage()->window()->getSize();
                if ($resize == static::RESIZE_FIT) {
                    $browser = $this->fitContent($browser);
                }

                $browser->screenshot($filename);

                if ($resize == static::RESIZE_FIT && $defaultSize) {
                    $browser->resize($defaultSize->getWidth(), $defaultSize->getHeight());
                }
            }

            $browser::$storeScreenshotsAt = $defaultStoreScreenshotsAt;
        }

        return $realFileName;
    }

    /**
     * @inheritDoc
     */
    public function fitContent(Browser $browser): Browser
    {
        try {
            $body = $this->getBodyElement($browser);
            $currentSize = $body->getSize();
            $browser->resize($currentSize->getWidth(), $currentSize->getHeight());
        } catch (\Exception $e) {
            $browser->fitContent();
        }

        return $browser;
    }

    /**
     * Get body element
     *
     * @param Browser $browser
     *
     * @return RemoteWebElement
     */
    protected function getBodyElement(Browser $browser): RemoteWebElement
    {
        if (is_callable($this->reporter::$getBodyElementCallback)) {
            return call_user_func($this->reporter::$getBodyElementCallback, $browser);
        }

        return $browser->driver->findElement(WebDriverBy::tagName('body'));
    }

    /**
     * Create combined report
     *
     * @param Browser $browser
     * @param string $filename
     *
     * @return Browser
     * @throws \ImagickException
     */
    protected function reportScreenCombined(Browser $browser, string $filename)
    {
        $windowSize = $browser->driver->manage()->window()->getSize();
        $windowHeight = $windowSize->getHeight();
        $body = $this->getBodyElement($browser);
        $fullHeight = $body->getSize()->getHeight();
        $counter = 0;
        $offset = 0;
        $files = [];
        while ($offset < $fullHeight) {
            $browser->driver->executeScript('window.scrollTo(0, ' . $offset . ');');
            if ($windowHeight > ($needCapture = ($fullHeight - $offset))) {
                $browser->resize($windowSize->getWidth(), $needCapture);
                $browser->driver->executeScript('window.scrollTo(0, document.body.scrollHeight);');
            }
            $browser->screenshot($screenName = "{$filename}_tmp-{$counter}");
            $files[] = $filePath = sprintf('%s/%s.' . $this->fileExt, rtrim($browser::$storeScreenshotsAt, '/'), $screenName);
            $counter++;
            $offset += $windowHeight;
        }
        $browser->resize($windowSize->getWidth(), $windowSize->getHeight());
        $browser->driver->executeScript('window.scrollTo(0, 0);');

        $im = new Imagick();
        foreach ($files as $file) {
            $im->readImage($file);
            unlink($file);
        }
        /* Append the images into one */
        $im->resetIterator();
        $combined = $im->appendImages(true);

        /* Output the image */
        $combined->setImageFormat($this->fileExt);
        $combined->writeImage(sprintf('%s/%s.' . $this->fileExt, rtrim($browser::$storeScreenshotsAt, '/'), $filename));

        return $browser;
    }

    /**
     * Find screenshot filename without overriding
     * @param Browser $browser
     * @param string $filename
     * @param string|null $suffix
     *
     * @return string
     */
    protected function fileName(Browser $browser, string $filename, ?string $suffix = null): string
    {
        $newFilename = $filename . ($suffix ? "_{$suffix}" : '');

        if (file_exists(sprintf('%s/%s.' . $this->fileExt, rtrim($browser::$storeScreenshotsAt, '/'), $newFilename))) {
            if (is_null($suffix)) {
                $suffix = 1;
            } elseif (is_numeric($suffix)) {
                $suffix = $suffix + 1;
            } else {
                $suffix = $suffix . '-' . Str::random();
            }

            return $this->fileName($browser, $filename, $suffix);
        }

        return $newFilename;
    }
}
