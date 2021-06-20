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
    protected Session $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    // TODO
    // create file called 'log' if not exists
    // if file exists check if the log count is less then 20000 (create a var for it)
    // if exists open that file and write to it
    //
    //

    public static function sAdd($msg)
    {
        echo "[" . date('Y-m-d H:i:s') . "] — " . $msg . PHP_EOL;
    }

    public function commit($msg, $level)
    {
        if (is_array($msg)) {
            foreach ($msg as $key => $value) {
                $msg[$key] = strip_tags($value);
            }
            $msg['level'] = $level;
        } else {
            $msg = ['message' => htmlentities($msg), 'level' => $level];
        }

        $logs = $this->session->get("logs");
        $logs[] = $msg;
        $this->session->set("logs", $logs);
    }
}
