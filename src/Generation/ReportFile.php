<?php


namespace ThinkOne\LaravelDuskReporter\Generation;

use Laravel\Dusk\Browser;
use ThinkOne\LaravelDuskReporter\Reporter;

class ReportFile implements ReportFileContract
{
    protected Reporter $reporter;

    protected array $data = [];

    protected string $name;

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

    /**
     * @inheritDoc
     */
    public function raw(string $content = ''): ReportFileContract
    {
        return $this->addContent(__FUNCTION__, $content);
    }

    /**
     * @inheritDoc
     */
    public function h1(string $content = ''): ReportFileContract
    {
        return $this->addContent(__FUNCTION__, $content);
    }

    /**
     * @inheritDoc
     */
    public function h2(string $content = ''): ReportFileContract
    {
        return $this->addContent(__FUNCTION__, $content);
    }

    /**
     * @inheritDoc
     */
    public function h3(string $content = ''): ReportFileContract
    {
        return $this->addContent(__FUNCTION__, $content);
    }

    /**
     * @inheritDoc
     */
    public function h4(string $content = ''): ReportFileContract
    {
        return $this->addContent(__FUNCTION__, $content);
    }

    /**
     * @inheritDoc
     */
    public function h5(string $content = ''): ReportFileContract
    {
        return $this->addContent(__FUNCTION__, $content);
    }

    /**
     * @inheritDoc
     */
    public function h6(string $content = ''): ReportFileContract
    {
        return $this->addContent(__FUNCTION__, $content);
    }

    /**
     * @inheritDoc
     */
    public function p(string $content = ''): ReportFileContract
    {
        return $this->addContent(__FUNCTION__, $content);
    }

    /**
     * @inheritDoc
     */
    public function br(int $count = 1): ReportFileContract
    {
        return $this->addContent(__FUNCTION__);
    }

    /**
     * @inheritDoc
     */
    public function image(string $url, string $alt = ''): ReportFileContract
    {
        return $this->addContent(__FUNCTION__, compact('url', 'alt'));
    }

    /**
     * @inheritDoc
     */
    public function screenshot(Browser $browser, string $suffix = '1', string $resize = 'fit'): ReportFileContract
    {
        $filename = $this->reporter->screenshoter()->make($browser, "{$this->name}_{$suffix}", $resize);

        return $this->addContent(__FUNCTION__, $filename);
    }

    protected function addContent(string $key, $content = null): self
    {
        $this->data[] = [
            'key' => $key,
            'content' => $content,
        ];

        return $this;
    }




    public function __destruct()
    {
        $path = rtrim(Reporter::$storeBuildAt, '/');

        $filePath = "{$path}/{$this->name}.json";
        $directoryPath = dirname($filePath);

        if (! is_dir($directoryPath)) {
            mkdir($directoryPath, 0777, true);
        }

        return file_put_contents($filePath, json_encode($this->data));
    }
}
