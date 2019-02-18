<?php

namespace Lib;

abstract class Singleton
{
    private static $instances = array();
    protected function __construct() {}
    public static function getInstance()
    {
        if (!isset(self::$instances[static::class])) {
            self::$instances[static::class] = new static();
        }
        return self::$instances[static::class];
    }
    private function __clone() {}
    private function __wakeup() {}
} 