<?php

namespace Src\Traits;

trait SingletonTrait
{
    private static $instance;
    public static function single(): self
    {
        if (empty(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }
    protected function __construct() {}
}
