<?php


namespace ThinkOne\LaravelDuskReporter\Generation;

use Laravel\Dusk\Browser;

interface ReportScreenshotContract
{
    /**
     * Type "FIT" - fits browser then make screenshot
     */
    const RESIZE_FIT = 'fit';

    /**
     * Type "COMBINE" - creates several screenshots and glues them
     */
    const RESIZE_COMBINE = 'combine';

    /**
     * Crete screenshot
     *
     * @param Browser $browser
     * @param string $filename
     * @param string|null $resize
     *
     * @return string
     * @throws \ImagickException
     */
    public function make(Browser $browser, string $filename, ?string $resize = null): string;

    /**
     * Fit browser content
     *
     * @param Browser $browser
     *
     * @return Browser
     */
    public function fitContent(Browser $browser): Browser;
}
