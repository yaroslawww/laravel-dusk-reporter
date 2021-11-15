<?php

namespace LaravelDuskReporter\Generation;

use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use LaravelDuskReporter\Reporter;

class ReportFile implements ReportFileContract
{
    protected Reporter $reporter;

    protected string $name;

    protected string $newLine;

    /**
     * ReportFile constructor.
     *
     * @param Reporter $reporter
     * @param string $name
     */
    public function __construct(Reporter $reporter, string $name)
    {
        $this->reporter = $reporter;
        $this->name     = $name;
        $this->setNewLine();

        $this->reporter->addToTableOfContents($this->fileName());
    }

    /**
     * @param string|null $newLine
     *
     * @return $this
     */
    public function setNewLine(?string $newLine = null): static
    {
        $this->newLine = is_string($newLine) ? $newLine : '<br>' . PHP_EOL;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isEmpty(): bool
    {
        $filePath = $this->reporter->reportFileName($this->fileName());
        clearstatcache();

        return !(file_exists($filePath) && filesize($filePath));
    }

    /**
     * @inheritDoc
     */
    public function raw(string $content = '', bool|string $newLine = false): static
    {
        return $this->addContent($content, $newLine);
    }

    /**
     * @inheritDoc
     */
    public function h1(string $content = '', bool|string $newLine = PHP_EOL . PHP_EOL): static
    {
        return $this->addContent("# {$content}", $newLine);
    }

    /**
     * @inheritDoc
     */
    public function h2(string $content = '', bool|string $newLine = PHP_EOL . PHP_EOL): static
    {
        return $this->addContent("## {$content}", $newLine);
    }

    /**
     * @inheritDoc
     */
    public function h3(string $content = '', bool|string $newLine = PHP_EOL . PHP_EOL): static
    {
        return $this->addContent("### {$content}", $newLine);
    }

    /**
     * @inheritDoc
     */
    public function h4(string $content = '', bool|string $newLine = PHP_EOL . PHP_EOL): static
    {
        return $this->addContent("#### {$content}", $newLine);
    }

    /**
     * @inheritDoc
     */
    public function h5(string $content = '', bool|string $newLine = PHP_EOL . PHP_EOL): static
    {
        return $this->addContent("##### {$content}", $newLine);
    }

    /**
     * @inheritDoc
     */
    public function h6(string $content = '', bool|string $newLine = PHP_EOL . PHP_EOL): static
    {
        return $this->addContent("###### {$content}", $newLine);
    }

    /**
     * @inheritDoc
     */
    public function p(string $content = '', bool|string $newLine = true): static
    {
        return $this->addContent($content, $newLine);
    }

    /**
     * @inheritDoc
     */
    public function br(int $count = 1, ?string $lineString = null): static
    {
        foreach (range(1, $count) as $num) {
            $this->addContent('', $lineString ?? true);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function list(array|\ArrayAccess $items = [], bool|string $newLine = PHP_EOL, string $styleType = '-'): static
    {
        if (count($items)) {
            foreach ($items as $item) {
                $this->listItem($item, true, $styleType);
            }
            if ($newLine) {
                $this->br(lineString: is_string($newLine) ? $newLine : null);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function listItem(string $content = '', bool|string $newLine = PHP_EOL, string $styleType = '-'): static
    {
        return $this->addContent("{$styleType} {$content}", $newLine);
    }

    /**
     * @inheritDoc
     */
    public function image(string $url, string $alt = '', bool|string $newLine = true): static
    {
        return $this->addContent("![{$alt}]({$url})", $newLine);
    }

    /**
     * @inheritDoc
     */
    public function link(string $url, string $text = '', bool|string $newLine = false): static
    {
        return $this->addContent("[{$text}]({$url})", $newLine);
    }

    /**
     * @inheritDoc
     */
    public function screenshot(Browser $browser, ?string $resize = null, ?string $suffix = null, bool|string $newLine = true): static
    {
        if ($this->reporter->isScreenshotsDisabled()) {
            return $this;
        }

        $filename = $filepath = $this->reporter->screenshoter()->make($browser, $this->fileName(), $resize, $suffix);

        if ($this->reporter->useScreenshotRelativePath()) {
            $filepath = $this->filePrefix() . Str::afterLast($filepath, $this->filePrefix());
        }

        return $this->addContent("![{$filename}](./{$filepath})", $newLine);
    }

    /**
     * @inheritDoc
     */
    public function screenshotWithVisibleScreen(Browser $browser, ?string $suffix = null, bool|string $newLine = true): static
    {
        return $this->screenshot($browser, null, $suffix, $newLine);
    }

    /**
     * @inheritDoc
     */
    public function screenshotWithFitScreen(Browser $browser, ?string $suffix = null, bool|string $newLine = true): static
    {
        return $this->screenshot($browser, ReportScreenshotContract::RESIZE_FIT, $suffix, $newLine);
    }

    /**
     * @inheritDoc
     */
    public function screenshotWithCombineScreen(Browser $browser, ?string $suffix = null, bool|string $newLine = true): static
    {
        return $this->screenshot($browser, ReportScreenshotContract::RESIZE_COMBINE, $suffix, $newLine);
    }

    /**
     * @return string
     */
    public function fileName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function filePrefix(): string
    {
        return Str::afterLast($this->fileName(), '/');
    }

    /**
     * Save content.
     *
     * @param string $content
     * @param bool|string $newLine
     *
     * @return static
     */
    protected function addContent(string $content = '', bool|string $newLine = true): static
    {
        return $this->appendToFile($content . (is_string($newLine) ? $newLine : ($newLine ? $this->newLine : '')));
    }

    /**
     * Append content to file.
     *
     * @param string $content
     *
     * @return static
     */
    protected function appendToFile(string $content): static
    {
        if (!$this->reporter->isReportingDisabled()) {
            $filePath = $this->reporter->reportFileName($this->fileName());

            if (!is_dir($directoryPath = dirname($filePath))) {
                mkdir($directoryPath, 0777, true);
            }

            file_put_contents($filePath, $content, FILE_APPEND | LOCK_EX);
        }

        return $this;
    }
}
