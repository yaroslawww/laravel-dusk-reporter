<?php


namespace ThinkOne\LaravelDuskReporter;

use Illuminate\Support\Str;
use ThinkOne\LaravelDuskReporter\Generation\ReportFileContract;

trait WithDuskReport
{
    protected ?ReportFileContract $globalDuskTestReportFile = null;

    /**
     * Create new report file
     *
     * @param string|null $initialisationFilename
     * @param \Closure|null $initialisationCallback
     *
     * @return ReportFileContract
     * @throws LaravelDuskReporterException
     */
    protected function duskReportFile(?string $initialisationFilename = null, ?\Closure $initialisationCallback = null): ReportFileContract
    {
        if (! $this->globalDuskTestReportFile) {
            if (! $initialisationFilename) {
                throw new LaravelDuskReporterException('On initialisation you should specify $initialisationFilename');
            }
            $this->globalDuskTestReportFile = $this->newDuskReportFile($initialisationFilename);
            if (is_callable($initialisationCallback) && $this->globalDuskTestReportFile->isEmpty()) {
                call_user_func_array($initialisationCallback, [ &$this->globalDuskTestReportFile ]);
            }
        }

        return $this->globalDuskTestReportFile;
    }

    /**
     * Create new report file
     *
     * @param string|null $filename
     *
     * @return ReportFileContract
     */
    protected function newDuskReportFile(?string $filename = null): ReportFileContract
    {
        return LaravelDuskReporter::newFile($filename ?? $this->duskReportFileName());
    }

    /**
     * Get File Name for Report
     *
     * @return string
     */
    protected function duskReportFileName(): string
    {
        $names = array_reverse(explode('\\', get_class($this)));
        $reportFolderName = rtrim(Str::ucfirst(Str::camel(($names[1] ?? '') . $names[0])), '/');

        return "{$reportFolderName}/{$this->getName(true)}";
    }
}
