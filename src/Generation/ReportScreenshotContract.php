<?php


namespace LaravelDuskReporter\Generation;

use Laravel\Dusk\Browser;

interface ReportScreenshotContract
{
    /**
     * Type "VISIBLE" - screen only visible part of browser.
     */
    const RESIZE_VISIBLE = 'visible';

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
     * @param string|null $suffix
     *
     * @return string
     * @throws \ImagickException
     */
    public function make(Browser $browser, string $filename, ?string $resize = null, ?string $suffix = null): string;

    /**
     * Fit browser content
     *
     * @param Browser $browser
     *
     * @return Browser
     */
    public function fitContent(Browser $browser): Browser;
}
