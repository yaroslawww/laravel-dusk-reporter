<?php


namespace LaravelDuskReporter;

use Carbon\Carbon;
use Illuminate\Support\Str;
use LaravelDuskReporter\Exceptions\LaravelDuskReporterException;
use LaravelDuskReporter\Generation\ReportFileContract;

trait TestWithDuskReport
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

    /**
     * Set up report file with initial data.
     *
     * @param string $name
     * @param string|null $title
     *
     * @return ReportFileContract
     * @throws LaravelDuskReporterException
     */
    protected function duskReportSetUp(string $name, ?string $title = null): ReportFileContract
    {
        $name = trim($name, '/');

        return $this->duskReportFile($name, function (ReportFileContract $file) use ($name, $title) {
            $file->h1($title ?? Str::title(Str::camel(Str::snake(Str::afterLast($name, '/'), ' '))));

            if (method_exists($this, 'duskReportFileInitialisationContent')) {
                $this->duskReportFileInitialisationContent($file);

                return;
            }

            $parts    = explode('/', $name);
            $backPath = Reporter::getValidFileName(Reporter::$indexFileBaseName);
            for ($i = 1; $i < count($parts); $i++) {
                $backPath = "../{$backPath}";
            }
            $file->link($backPath, 'Go home')->br()
                 ->listItem('Test Date: ' . Carbon::now()->format('Y-m-d H:i:s'));

            if (method_exists($this, 'duskReportFileInitialisationAdditionalContent')) {
                $this->duskReportFileInitialisationAdditionalContent($file);
            }
        });
    }

    /**
     * Set up report file with initial data using class name.
     *
     * @param string|null $path
     * @param string|null $title
     *
     * @return ReportFileContract
     * @throws LaravelDuskReporterException
     */
    protected function duskReportSetUpUsingTestClassName(?string $path = null, ?string $title = null): ReportFileContract
    {
        $path = $path ? (rtrim($path, '/') . '/') : '';
        $name = $path . trim(Str::beforeLast(class_basename(get_class($this)), 'Test'), '/');

        return $this->duskReportSetUp($name, $title);
    }

    /**
     * Add new heading to file using method name
     *
     * @param string $methodName
     * @param string $headerType
     *
     * @return ReportFileContract
     * @throws LaravelDuskReporterException
     */
    protected function duskReportSetHeadingFromTestMethod(string $methodName, string $headerType = 'h2'): ReportFileContract
    {
        return call_user_func([
            $this->duskReportFile(),
            $headerType,
        ], Str::title(Str::snake(Str::camel(Str::after($methodName, 'test')), ' ')));
    }
}
