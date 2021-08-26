<?php

namespace Modules;

define('ENV', 'DEV');
/**
 * Class DD
 *
 * @author Roman Zakhriapa <utmostcreator@gmail.com>
 * @package modules/DD
 */

function getVariableName($var)
{
    foreach ($GLOBALS as $varName => $value) {
        if ($value === $var) {
            return $varName;
        }
    }
    return;
}

class DD
{
    private static float $startTime = 0.0;
    private static float $endTime = 0.0;

    public static function dd($data, $shotHTML = false, $exit = true)
    {
        if (ENV !== 'DEV') {
            echo 'Debug is not allowed here!';
            exit;
        }
        echo DD::getStyles();
        echo "<br><div class='wrapper'>";
        echo DD::dumpFileInfo();
        echo "<div class='centered-content'><pre class='colored wrap-any-text'>";
        echo '$' . getVariableName($data) . PHP_EOL;
        $output = '';
        if ($shotHTML) {
            $output .= htmlspecialchars(str_replace(';', ";\n", serialize($data)));
        } else {
            var_dump($data);
        }
        $output .= "</pre></div></div>";

        echo $output;
//        ob_end_clean();

        if ($exit) {
            exit;
        }
    }

    public static function dl($data, $shotHTML = false, $dumpInfo = false)
    {
        echo "<pre class='wrap-any-text'>";
        if ($dumpInfo) {
            echo DD::dumpFileInfo();
        }

        echo '$' . getVariableName($data) . PHP_EOL;
        if ($shotHTML) {
            echo htmlspecialchars(str_replace(';', ";\n", serialize($data)));
        } else {
            var_dump($data);
        }
        echo "</pre>";
    }

    public static function startTimeTracking()
    {
        self::$startTime = microtime(true);
    }

    public static function getEndResultTime()
    {
        self::$startTime;

        self::$endTime = microtime(true);
        $duration = self::$endTime - self::$startTime;
        $ms = round($duration * 1000, 3);
        $s = round($duration, 3);
//DD::dd($s);
        return "<span title='Total tile to complete the whole script took {$ms} ms;'>{$s} sec;</span>";
//        echo 'Your script needs ' . (self::$endTime - self::$startTime) . ' ms/s to execute' . PHP_EOL;
    }

    public static function ddAnyArgs()
    {
        $numArgs = func_num_args();
        echo "<pre style='white-space: pre-wrap;'>";
        echo 'Number of arguments:' . $numArgs . "\n";

        if ($numArgs >= 2) {
            echo 'Second argument is: ' . func_get_arg(1) . "\n";
        }

        $args = func_get_args();
        foreach ($args as $index => $arg) {
            var_dump('Argument' . $index . ' is ' . $arg . "\n");
            unset($args[$index]);
        }
        echo "</pre>";
        exit;
    }

    private static function getStyles()
    {
        return "<style>   
body {
    background-color: lightblue;
    align-items: center;
} 
div.centered-content {
    max-width: 75%;
}
div.wrapper, body {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-flow: column;
}

pre.colored {
    min-width: 50vh;
    border-radius: 2%;
    padding: 20px 20px;
    font-weight: bold;
    line-height: 28px;
    width: 100%;
    background-color: floralwhite;
    font-size: 18px;
}
.wrap-any-text {
    word-break: break-word;
    word-spacing: normal;
    white-space: pre-wrap; /* Since CSS 2.1 */
    white-space: -moz-pre-wrap; /* Mozilla, since 1999 */
    white-space: -pre-wrap; /* Opera 4-6 */
    white-space: -o-pre-wrap; /* Opera 7 */
    word-wrap: break-word; /* Internet Explorer 5.5+ */
}
.file-info {
    max-width: 70%;
    font-size: 24px;
    font-weight: 600;
}
</style>";
    }

    private static function dumpFileInfo()
    {
        echo '===============';
        $trace = debug_backtrace();
//        echo "<pre>";
//        var_dump($trace);
        $shortDumpInfo = '<pre class="file-info wrap-any-text">';
        if (isset($trace[1])) {
            $shortDumpInfo .= "Called IN FILE \n{$trace[1]['file']}" . PHP_EOL;
            $shortDumpInfo .= "ON LINE: " . $trace[1]['line'] . " IN FILE: " . PHP_EOL . PHP_EOL;
        }
        if (isset($trace[2])) {
            $shortDumpInfo .= isset($trace[2]['class']) ? "Class: " . $trace[2]['class'] : 'File: ' . $trace[2]['file'];
            $shortDumpInfo .= isset($trace[2]['type']) ?? $trace[2]['type'] . $trace[2]['function'] . PHP_EOL;
            $shortDumpInfo .= isset($trace[2]['function']) ?? $trace[2]['function'] . PHP_EOL;

            if (isset($trace[2]['args']) && is_array(isset($trace[2]['args']))) {
                $shortDumpInfo .= 'Args: ' . implode(' | ', $trace[2]['args']) . '<hr>';
            }
        }
        $shortDumpInfo .= '</pre>';
        return $shortDumpInfo;
    }

    public static function de($data, $shotHTML = true)
    {

        echo "<pre>";
        if ($shotHTML) {
            echo htmlspecialchars(str_replace(';', ";\n", serialize($data)));
        } else {
            var_dump($data);
        }
        echo "</pre>";
    }
}
