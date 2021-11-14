<?php

namespace LaravelDuskReporter;

use LaravelDuskReporter\Generation\ReportFileContract;

trait HasDuskReporter {
    public static ?ReportFileContract $duskReporter = null;

    /**
     * Set dusk reporter.
     *
     * @param ReportFileContract $reporter
     *
     * @return void
     */
    public static function withDuskReporter( ReportFileContract $reporter ): void {
        static::$duskReporter = $reporter;
    }
}
