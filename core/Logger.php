<?php

namespace app\core;

/**
 * Class Logger
 *
 * @author Roman Zakhriapa <utmostcreator@gmail.com>
 * @package app\core
 */
class Logger
{

    // TODO
    // create file called 'log' if not exists
    // if file exists check if the log count is less then 20000 (create a var for it)
    // if exists open that file and write to it
    //
    //

    public static function sAdd($msg)
    {
        echo "[" . date('Y-m-d H:i:s') . "] — " .  $msg . PHP_EOL;
    }
}
