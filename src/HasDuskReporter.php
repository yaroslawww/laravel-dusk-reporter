<?php

namespace LaravelDuskReporter;

use LaravelDuskReporter\Generation\ReportFileContract;

trait HasDuskReporter
{
    public ?ReportFileContract $duskReporter = null;

    /**
     * Get dusk reporter instance.
     *
     * @return ReportFileContract|null
     */
    public function duskReporter(): ?ReportFileContract
    {
        return $this->duskReporter;
    }

    /**
     * Check is object has dusk reporter.
     *
     * @return bool
     */
    public function hasDuskReporter(): bool
    {
        return !is_null($this->duskReporter());
    }

    /**
     * Set dusk reporter to object.
     *
     * @param ReportFileContract|null $reporter
     *
     * @return $this
     */
    public function setDuskReporter(?ReportFileContract $reporter): static
    {
        $this->duskReporter = $reporter;

        return $this;
    }

    /**
     * Initialise instance with dusk reporter.
     *
     * @param ReportFileContract $reporter
     * @param array $params
     *
     * @return static
     */
    public static function makeWithDuskReporter(ReportFileContract $reporter, array $params = []): static
    {
        return ( new static(...$params) )->setDuskReporter($reporter);
    }
}
