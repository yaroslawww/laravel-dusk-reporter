<?php

namespace LaravelDuskReporter\Generation;

use Laravel\Dusk\Browser;

interface ReportFileContract
{

    /**
     * Add raw string
     *
     * @param string $content
     * @param bool|string $newLine
     *
     * @return static
     */
    public function raw(string $content = '', bool|string $newLine = false): static;

    /**
     * Add header #1
     *
     * @param string $content
     * @param bool|string $newLine
     *
     * @return static
     */
    public function h1(string $content = '', bool|string $newLine = true): static;

    /**
     * Add header #2
     *
     * @param string $content
     * @param bool|string $newLine
     *
     * @return static
     */
    public function h2(string $content = '', bool|string $newLine = true): static;

    /**
     * Add header #3
     *
     * @param string $content
     * @param bool|string $newLine
     *
     * @return static
     */
    public function h3(string $content = '', bool|string $newLine = true): static;

    /**
     * Add header #4
     *
     * @param string $content
     * @param bool|string $newLine
     *
     * @return static
     */
    public function h4(string $content = '', bool|string $newLine = true): static;

    /**
     * Add header #5
     *
     * @param string $content
     * @param bool|string $newLine
     *
     * @return static
     */
    public function h5(string $content = '', bool|string $newLine = true): static;

    /**
     * Add header #6
     *
     * @param string $content
     * @param bool|string $newLine
     *
     * @return static
     */
    public function h6(string $content = '', bool|string $newLine = true): static;

    /**
     * Add break line
     *
     * @param int $count
     *
     * @return static
     */
    public function br(int $count = 1): static;

    /**
     * Add list
     *
     * @param array|\ArrayAccess|String[] $items
     * @param bool|string $newLine
     * @param string $styleType
     *
     * @return static
     */
    public function list(array|\ArrayAccess $items = [], bool|string $newLine = true, string $styleType = '-'): static;

    /**
     * Add list item
     *
     * @param string $content
     * @param bool|string $newLine
     * @param string $styleType
     *
     * @return static
     */
    public function listItem(string $content = '', bool|string $newLine = true, string $styleType = '-'): static;

    /**
     * Add Image
     *
     * @param string $url
     * @param string $alt
     * @param bool|string $newLine
     *
     * @return static
     */
    public function image(string $url, string $alt = '', bool|string $newLine = true): static;

    /**
     * Add Link
     *
     * @param string $url
     * @param string $text
     * @param bool|string $newLine
     *
     * @return static
     */
    public function link(string $url, string $text = '', bool|string $newLine = true): static;

    /**
     * Make screenshot
     *
     * @param Browser $browser
     * @param string|null $resize
     * @param string|null $suffix
     * @param bool|string $newLine
     *
     * @return static
     */
    public function screenshot(Browser $browser, ?string $resize = null, ?string $suffix = null, bool|string $newLine = true): static;

    /**
     * Make screenshot with visible screen
     *
     * @param Browser $browser
     * @param string|null $suffix
     * @param bool|string $newLine
     *
     * @return static
     */
    public function screenshotWithVisibleScreen(Browser $browser, ?string $suffix = null, bool|string $newLine = true): static;

    /**
     * Make screenshot with fit screen
     *
     * @param Browser $browser
     * @param string|null $suffix
     * @param bool|string $newLine
     *
     * @return static
     */
    public function screenshotWithFitScreen(Browser $browser, ?string $suffix = null, bool|string $newLine = true): static;

    /**
     * Make screenshot with "combine" screen
     *
     * @param Browser $browser
     * @param string|null $suffix
     * @param bool|string $newLine
     *
     * @return static
     */
    public function screenshotWithCombineScreen(Browser $browser, ?string $suffix = null, bool|string $newLine = true): static;

    /**
     * @param string|null $newLine
     *
     * @return static
     */
    public function setNewLine(?string $newLine): static;

    /**
     * @return bool
     */
    public function isEmpty(): bool;
}
