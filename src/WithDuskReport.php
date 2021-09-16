<?php


namespace LaravelDuskReporter;

use Carbon\Carbon;
use Illuminate\Support\Str;
use LaravelDuskReporter\Exceptions\LaravelDuskReporterException;
use LaravelDuskReporter\Generation\ReportFileContract;

trait WithDuskReport
{
    protected ?ReportFileContract $globalDuskTestReportFile = null;

    /**
     * Create report file for global usage
     *
     * @param string|null $initialisationFilename
     * @param \Closure|null $initialisationCallback
     *
     * @return ReportFileContract
     * @throws LaravelDuskReporterException
     */
    protected function duskReportFile(?string $initialisationFilename = null, ?\Closure $initialisationCallback = null): ReportFileContract
    {
        if (!$this->globalDuskTestReportFile) {
            if (!$initialisationFilename) {
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
        $names            = array_reverse(explode('\\', get_class($this)));
        $reportFolderName = rtrim(Str::ucfirst(Str::camel(($names[1] ?? '') . $names[0])), '/');

        if (method_exists($this, 'getDuskReportFileName')) {
            $name = $this->getDuskReportFileName();
        } elseif (method_exists($this, 'getName')) {
            $name = $this->getName();
        } else {
            $name = Carbon::now()->format('Y_m_d_h_i_s-') . Str::random(60);
        }

        return "{$reportFolderName}/{$name}";
    }
}
