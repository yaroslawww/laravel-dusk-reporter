<?php

namespace ThinkOne\LaravelDuskReporter\Generation;

use Laravel\Dusk\Browser;

interface ReportFileContract
{

    /**
     * Add raw string
     *
     * @param string $content
     * @param bool $newLine
     *
     * @return $this
     */
    public function raw(string $content = '', $newLine = true): self;

    /**
     * Add header #1
     *
     * @param string $content
     * @param bool $newLine
     *
     * @return $this
     */
    public function h1(string $content = '', $newLine = true): self;

    /**
     * Add header #2
     *
     * @param string $content
     * @param bool $newLine
     *
     * @return $this
     */
    public function h2(string $content = '', $newLine = true): self;

    /**
     * Add header #3
     *
     * @param string $content
     * @param bool $newLine
     *
     * @return $this
     */
    public function h3(string $content = '', $newLine = true): self;

    /**
     * Add header #4
     *
     * @param string $content
     * @param bool $newLine
     *
     * @return $this
     */
    public function h4(string $content = '', $newLine = true): self;

    /**
     * Add header #5
     *
     * @param string $content
     * @param bool $newLine
     *
     * @return $this
     */
    public function h5(string $content = '', $newLine = true): self;

    /**
     * Add header #6
     *
     * @param string $content
     * @param bool $newLine
     *
     * @return $this
     */
    public function h6(string $content = '', $newLine = true): self;

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
     * @param bool $newLine
     *
     * @return $this
     */
    public function image(string $url, string $alt = '', $newLine = true): self;

    /**
     * Add Link
     *
     * @param string $url
     * @param string $text
     * @param bool $newLine
     *
     * @return $this
     */
    public function link(string $url, string $text = '', $newLine = true): self;

    /**
     * Make screenshot
     *
     * @param Browser $browser
     * @param string $suffix
     * @param string|null $resize
     * @param bool $newLine
     *
     * @return $this
     */
    public function screenshot(Browser $browser,  string $suffix = '1', ?string $resize = null, $newLine = true): self;

    /**
     * @param string|null $newLine
     *
     * @return $this
     */
    public function setNewLine(?string $newLine): self;

    /**
     * @return bool
     */
    public function isEmpty(): bool;
}
