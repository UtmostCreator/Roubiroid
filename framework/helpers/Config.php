<?php

namespace Framework\helpers;

use Framework\Paths;

class Config
{
    protected static array $data;

    public static function init()
    {
        if (empty(self::$data)) {
            self::$data = require_once Paths::getBase() . '/config/config.php';
        }
    }

    public static function get($pathTo = '')
    {
        self::init();
        if (empty($pathTo)) {
            return self::$data;
        }

        if (strpos($pathTo, '=') !== false) {
            $parts = explode('=', $pathTo);
            if (count($parts) === 2) {
                $secondParamName = self::$data[$parts[1]];
                return self::$data[$parts[0]][$secondParamName];
            }

            throw new \InvalidArgumentException('You have provided a wrong syntax.');
        }

        return self::fetch($pathTo, self::$data);
    }

    private static function fetch(string $pathTo, $specificConfig)
    {
        if (strpos($pathTo, '.') === false) {
            return $specificConfig[$pathTo] ?? $specificConfig;
        }
        $parts = explode('.', $pathTo);
        $pathTo = substr($pathTo, strlen($parts[0]) + 1);
        $specificConfig = $specificConfig[$parts[0]] ?? $specificConfig;
        return self::fetch($pathTo, $specificConfig);
    }
}
