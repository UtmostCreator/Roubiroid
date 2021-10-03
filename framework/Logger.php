<?php

namespace Framework;

/**
 * Class Logger
 *
 * @author Roman Zakhriapa <utmostcreator@gmail.com>
 * @package Framework
 */
// TODO REWRITE it completely
class Logger
{
    protected Session $session;
    private static ?Logger $inst = null;

    private function __construct()
    {
    }

    public static function getInst(): self
    {
        if (is_null(self::$inst)) {
            return new self();
        }

        return self::$inst;
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

        // TODO create file logger
//        $logs = $this->session->get("logs");
//        $logs[] = $msg;
//        $this->session->set("logs", $logs);
    }
}
