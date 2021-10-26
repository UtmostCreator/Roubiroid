<?php

namespace Framework\helpers;

use Framework\Paths;
use Modules\DD;

class Config
{
    protected static array $data;

    public static function get($pathTo = '')
    {
        if (empty(self::$data)) {
            self::$data = require_once Paths::getBase() . '/config/config.php';
        }
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
//        DD::dl($parts);
        $pathTo = substr($pathTo, strlen($parts[0]) + 1);
//        DD::dl($pathTo);
//        DD::dd($specificConfig);
        $specificConfig = $specificConfig[$parts[0]] ?? $specificConfig;
        return self::fetch($pathTo, $specificConfig);
    }
}
