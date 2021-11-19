<?php

namespace LaravelDuskReporter;

use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use LaravelDuskReporter\Generation\ReportFileContract;

trait HasDuskReporter
{
    public static ?ReportFileContract $duskReporter = null;

    /**
     * Set dusk reporter.
     *
     * @param ReportFileContract $reporter
     *
     * @return void
     */
    public static function withDuskReporter(?ReportFileContract $reporter): void
    {
        static::$duskReporter = $reporter;
    }

    /**
     * Callback to add text to report file.
     *
     * @param Browser $browser
     * @param \Closure $callback
     *
     * @return Browser
     */
    public function reportAppend(Browser $browser, \Closure $callback): Browser
    {
        if (!is_null(static::$duskReporter)) {
            call_user_func($callback, static::$duskReporter, $browser);
        }

        return $browser;
    }

    /**
     * Add to report information about current page.
     *
     * @param Browser $browser
     * @param string|null $pageName
     * @param array $options
     *
     * @return Browser
     */
    public function reportUserSeePage(Browser $browser, ?string $pageName = null, array $options = []): Browser
    {
        if (!is_null(static::$duskReporter)) {
            if (isset($options['screenshot']) && is_array($options['screenshot'])) {
                call_user_func_array([ static::$duskReporter, 'screenshot' ], $options['screenshot']);
            } else {
                static::$duskReporter->screenshotWithCombineScreen($browser);
            }
            static::$duskReporter->p(trans('dusk-reporter::report.user_see_page', [
                'page' => $pageName ?? Str::title(Str::kebab(Str::beforeLast(class_basename(get_class($this)), 'Page'), ' ')),
            ]));
        }

        return $browser;
    }
}
