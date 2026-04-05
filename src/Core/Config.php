<?php

namespace Anderson\XboxLive\Core;

use Dotenv\Dotenv;

class Config
{
    private static $instance = null;
    private $data = [];

    private function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->safeLoad();
    }

    public static function get($key, $default = null)
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }

    public static function init()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
    }
}
