<?php


namespace Silktide\Stash;


class DependencyRegistry
{
    protected static $registry = [];

    public static function load($key, callable $callable)
    {
        if (!isset(self::$registry[$key])) {
            self::$registry[$key] = $callable();
        }
        return self::$registry[$key];
    }
}