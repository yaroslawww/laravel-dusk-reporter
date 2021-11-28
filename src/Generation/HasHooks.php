<?php

namespace LaravelDuskReporter\Generation;

use Illuminate\Support\Str;

trait HasHooks
{
    protected static array $hookActions = [];

    public static function addHookAction(string $name, \Closure $callback, int $order = 10)
    {
        if (!isset(static::$hookActions[$name]) || !is_array(static::$hookActions[$name])) {
            static::$hookActions[$name] = [];
        }
        if (!isset(static::$hookActions[$name][$order]) || !is_array(static::$hookActions[$name][$order])) {
            static::$hookActions[$name][$order] = [];
        }
        static::$hookActions[$name][$order][] = $callback;
    }

    protected function runHookAction(string $name, ...$arguments)
    {
        if (isset(static::$hookActions[$name]) && is_array(static::$hookActions[$name])) {
            sort(static::$hookActions[$name]);
            foreach (static::$hookActions[$name] as $orderedActions) {
                foreach ($orderedActions as $action) {
                    if (is_callable($action)) {
                        call_user_func($action, ...$arguments);
                    }
                }
            }
        }
    }

    public static function __callStatic(string $name, array $arguments)
    {
        if (
            Str::startsWith($name, 'addAction')
            && ($hookAction = Str::camel(Str::after($name, 'addAction')))
        ) {
            static::addHookAction($hookAction, ...$arguments);

            return;
        }

        throw new \BadMethodCallException("Method [$name] not exists");
    }
}
