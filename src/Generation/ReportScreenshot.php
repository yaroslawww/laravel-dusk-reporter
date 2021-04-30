<?php


namespace ThinkOne\LaravelDuskReporter\Generation;

use Facebook\WebDriver\WebDriverBy;
use Imagick;
use Laravel\Dusk\Browser;
use ThinkOne\LaravelDuskReporter\Reporter;

class ReportScreenshot
{
    protected Reporter $reporter;

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
     * Crete screenshot
     * @param Browser $browser
     * @param string $filename
     * @param string $resize
     *
     * @return string
     * @throws \ImagickException
     */
    public function make(Browser $browser, string $filename, string $resize = 'fit'): string
    {
        $defaultStoreScreenshotsAt = $browser::$storeScreenshotsAt;

        $browser::$storeScreenshotsAt = $this->reporter->storeScreenshotAt();

        if ($resize == 'combine') {
            $this->reportScreenCombined($browser, $filename);
        } else {
            $defaultSize = $browser->driver->manage()->window()->getSize();
            if ($resize == 'fit') {
                $browser = $this->fitContent($browser);
            }

            $browser->screenshot($filename);

            if ($resize == 'fit' && $defaultSize) {
                $browser->resize($defaultSize->getWidth(), $defaultSize->getHeight());
            }
        }

        $browser::$storeScreenshotsAt = $defaultStoreScreenshotsAt;

        return "{$filename}.png";
    }


    public function fitContent(Browser $browser)
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

    protected function getBodyElement(Browser $browser): \Facebook\WebDriver\Remote\RemoteWebElement
    {
        if (is_callable($this->reporter::$getBodyElementCallback)) {
            return call_user_func($this->reporter::$getBodyElementCallback, $browser);
        }

        return $browser->driver->findElement(WebDriverBy::tagName('body'));
    }

    /**
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
            $browser->screenshot($screenName = "{$filename}_{$counter}");
            $files[] = $filePath = sprintf('%s/%s.png', rtrim($browser::$storeScreenshotsAt, '/'), $screenName);
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
        $combined->setImageFormat('png');
        $combined->writeImage(sprintf('%s/%s.png', rtrim($browser::$storeScreenshotsAt, '/'), $filename));

        return $browser;
    }
}
