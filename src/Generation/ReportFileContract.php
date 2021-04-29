<?php

namespace ThinkOne\LaravelDuskReporter\Generation;

use Laravel\Dusk\Browser;

interface ReportFileContract
{

    /**
     * Add raw string
     *
     * @param string $content
     *
     * @return $this
     */
    public function raw(string $content = ''): self;

    /**
     * Add header #1
     *
     * @param string $content
     *
     * @return $this
     */
    public function h1(string $content = ''): self;

    /**
     * Add header #2
     *
     * @param string $content
     *
     * @return $this
     */
    public function h2(string $content = ''): self;

    /**
     * Add header #3
     *
     * @param string $content
     *
     * @return $this
     */
    public function h3(string $content = ''): self;

    /**
     * Add header #4
     *
     * @param string $content
     *
     * @return $this
     */
    public function h4(string $content = ''): self;

    /**
     * Add header #5
     *
     * @param string $content
     *
     * @return $this
     */
    public function h5(string $content = ''): self;

    /**
     * Add header #6
     *
     * @param string $content
     *
     * @return $this
     */
    public function h6(string $content = ''): self;

    /**
     * Add paragraph
     *
     * @param string $content
     *
     * @return $this
     */
    public function p(string $content = ''): self;

    /**
     * Add break line
     *
     * @param int $count
     *
     * @return $this
     */
    public function br(int $count = 1): self;

    /**
     * Add Image
     *
     * @param string $url
     * @param string $alt
     *
     * @return $this
     */
    public function image(string $url, string $alt = ''): self;

    /**
     * Make screenshot
     *
     * @param Browser $browser
     * @param string $suffix
     * @param string $resize
     *
     * @return $this
     */
    public function screenshot(Browser $browser,  string $suffix = '1', string $resize = 'fit'): self;
}
