<?php


namespace ThinkOne\LaravelDuskReporter;

use Illuminate\Support\Str;
use ThinkOne\LaravelDuskReporter\Generation\ReportFileContract;

trait WithDuskReport
{

    /**
     * Get File Name for Report
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
