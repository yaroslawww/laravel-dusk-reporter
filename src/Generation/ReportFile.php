<?php

namespace ThinkOne\LaravelDuskReporter\Generation;

use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use ThinkOne\LaravelDuskReporter\Reporter;

class ReportFile implements ReportFileContract
{
    protected Reporter $reporter;

    protected string $name;

    protected string $newLine = PHP_EOL;

    /**
     * ReportFile constructor.
     *
     * @param Reporter $reporter
     * @param string $name
     */
    public function __construct(Reporter $reporter, string $name)
    {
        $this->reporter = $reporter;
        $this->name = $name;
    }

    public function setNewLine(?string $newLine): self
    {
        $this->newLine = is_string($newLine) ? $newLine : PHP_EOL;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isEmpty(): bool
    {
        $filePath = $this->fullFileName();
        clearstatcache();

        return ! (file_exists($filePath) && filesize($filePath));
    }

    /**
     * @inheritDoc
     */
    public function raw(string $content = '', $newLine = false): self
    {
        return $this->addContent($content, $newLine);
    }

    /**
     * @inheritDoc
     */
    public function h1(string $content = '', $newLine = true): self
    {
        return $this->addContent("# {$content}", $newLine);
    }

    /**
     * @inheritDoc
     */
    public function h2(string $content = '', $newLine = true): self
    {
        return $this->addContent("## {$content}", $newLine);
    }

    /**
     * @inheritDoc
     */
    public function h3(string $content = '', $newLine = true): self
    {
        return $this->addContent("### {$content}", $newLine);
    }

    /**
     * @inheritDoc
     */
    public function h4(string $content = '', $newLine = true): self
    {
        return $this->addContent("#### {$content}", $newLine);
    }

    /**
     * @inheritDoc
     */
    public function h5(string $content = '', $newLine = true): self
    {
        return $this->addContent("##### {$content}", $newLine);
    }

    /**
     * @inheritDoc
     */
    public function h6(string $content = '', $newLine = true): self
    {
        return $this->addContent("###### {$content}", $newLine);
    }

    /**
     * @inheritDoc
     */
    public function br(int $count = 1): self
    {
        foreach (range(1, $count) as $num) {
            $this->addContent('', true);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function image(string $url, string $alt = '', $newLine = true): self
    {
        return $this->addContent("![{$alt}]({$url})", $newLine);
    }

    /**
     * @inheritDoc
     */
    public function link(string $url, string $text = '', $newLine = true): self
    {
        return $this->addContent("[{$text}]({$url})", $newLine);
    }

    /**
     * @inheritDoc
     */
    public function screenshot(Browser $browser, string $suffix = '1', ?string $resize = null, $newLine = true): self
    {
        $name = "{$this->fileName()}_{$suffix}";
        $filename = $this->reporter->screenshoter()->make($browser, $name, $resize);

        if ($this->reporter->useScreenshotRelativePath()) {
            $filename = $this->filePrefix() . Str::afterLast($filename, $this->filePrefix());
        }

        return $this->addContent("![{$name}]({$filename})", $newLine);
    }

    public function fullFileName(): string
    {
        $path = $this->reporter->storeBuildAt();

        return "{$path}/{$this->fileName()}.md";
    }

    public function fileName(): string
    {
        return $this->name;
    }

    public function filePrefix(): string
    {
        $array = array_reverse(explode('/', $this->fileName()));

        return $array[0] ?? '';
    }


    protected function addContent(string $content = '', $newLine = true): self
    {
        $suffix = '';
        if ($newLine) {
            $suffix = is_string($newLine) ? $newLine : $this->newLine;
        }

        $this->appendToFile($content . $suffix);

        return $this;
    }

    /**
     * @param string $content
     *
     * @return void
     */
    protected function appendToFile(string $content): void
    {
        if (! $this->reporter->isReportingDisabled()) {
            $filePath = $this->fullFileName();

            $directoryPath = dirname($filePath);

            if (! is_dir($directoryPath)) {
                mkdir($directoryPath, 0777, true);
            }

            file_put_contents($filePath, $content, FILE_APPEND | LOCK_EX);
        }
    }
}
