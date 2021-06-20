<?php

namespace app\core;

use modules\DD\DD;

class ErrorHandler
{
    protected const SIMPLE = 'simple';
    protected const TABLE = 'table';
    protected string $errorMessage;
    protected string $format = self::SIMPLE;
    protected Logger $logger;
    protected array $errors = [];

    /**
     * Storing handled Exceptions names,
     * Return it depending on a code status
     *
     * @param $error
     * @return string
     */
    public static function getErrorName($error): string
    {
        $errors = [
            E_ERROR => 'ERROR',
            E_WARNING => 'WARNING',
            E_PARSE => 'PARSE',
            E_NOTICE => 'NOTICE',
            E_CORE_ERROR => 'CORE_ERROR',
            E_CORE_WARNING => 'CORE_WARNING',
            E_COMPILE_ERROR => 'COMPILE_ERROR',
            E_COMPILE_WARNING => 'COMPILE_WARNING',
            E_USER_ERROR => 'USER_ERROR',
            E_USER_WARNING => 'USER_WARNING',
            E_USER_NOTICE => 'USER_NOTICE',
            E_STRICT => 'STRICT',
            E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
            E_DEPRECATED => 'DEPRECATED',
            E_USER_DEPRECATED => 'USER_DEPRECATED',
        ];
        if (array_key_exists($error, $errors)) {
            return $errors[$error] . " [$error]";
        }

        return $error;
    }

    public function __construct($logger)
    {
        $this->logger = $logger;
    }

    public function register()
    {
        // to catch general error like include/require;
        set_error_handler(\Closure::fromCallable([$this, 'handle']));

        // to catch Fatal Error that can not be handled by set_error_handler; e.g. no semicolon
        register_shutdown_function(\Closure::fromCallable([$this, 'handleFatalError']));

        set_exception_handler(\Closure::fromCallable([$this, 'exceptionHandler']));
    }

    /**
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @return bool
     * if it returns TRUE - the error handling is ended and no more handlers can be used;
     * if it returns FALSE - the error handing is proceeded and passed to a new handler;
     * 5th param can be $errContext = null;
     */
    protected function handle(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        $this->addError($errno, $errstr, $errfile, $errline);
        return true;
    }

    private function addError(int $errno, string $errstr, string $errfile, int $errline, int $httpStatusCode = 500)
    {
        $httpStatusMsg = 'Web server is down';
        $phpSapiName = substr(php_sapi_name(), 0, 3);
        if (!headers_sent()) {
            if ($phpSapiName == 'cgi' || $phpSapiName == 'fpm') {
                header('Status: ' . $httpStatusCode . ' ' . $httpStatusMsg);
            } else {
                $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
                header($protocol . ' ' . $httpStatusCode . ' ' . $httpStatusMsg);
            }
        }

        $errNoStr = self::getErrorName($errno);
        $simpleMsg = $this->getSimple($errNoStr, $errstr, $errfile, $errline, $httpStatusCode);
        if ($this->format === self::SIMPLE) {
            $this->errorMessage = $simpleMsg;
        } else {
            $this->errorMessage = $this->getInfoInTable($errNoStr, $errstr, $errfile, $errline, $httpStatusCode);
        }

        $errorArr = [
            'errno' => $errno,
            'errstr' => $errstr,
            'errfile' => $errfile,
            'errline' => $errline,
            'httpStatusCode' => $httpStatusCode,
        ];
        $loggedError = $this->format === self::SIMPLE ? $this->errorMessage : $simpleMsg;
        $this->writeHistoryLog($errno, $errorArr);

        $this->errors[] = $this->getErrorMessage();
//        exit($this->errorMessage);
    }

    /** It will be called e.g. on unknown_function() */
    protected function handleFatalError(): bool
    {
        $lasterror = error_get_last();
//        DD::dd($lasterror);
        if (!empty($lasterror)) {
            switch ($lasterror['type']) {
                case E_ERROR:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                case E_RECOVERABLE_ERROR:
                case E_CORE_WARNING:
                case E_COMPILE_WARNING:
                case E_PARSE:
                if (ob_get_length()) {
                    ob_end_clean();
                }
                    $this->errors[] = $this->addError($lasterror['type'], '[SHUTDOWN]' . $this->getHumanReadableTrace($lasterror['message']), $lasterror['file'], $lasterror['line']);
//                $this->logger->commit($lasterror, "fatal");
            }
        }
        $this->displayErrors();
        return true;
    }

    protected function getInfoInTable(string $errno, string $errstr, string $errfile, int $errline, int $httpStatusCode): string
    {
        $trace = $this->getHumanReadableTrace();
        $content = "
            <table>
                <thead><th>Item</th><th>Description</th></thead>
                <tbody>
                    <tr>
                        <th>Error</th>
                        <td><pre>$errstr</pre></td>
                    </tr>
                    <tr>
                        <th>Errno</th>
                        <td><pre>$errno</pre></td>
                    </tr>
                    <tr>
                        <th>File</th>
                        <td>$errfile</td>
                    </tr>
                    <tr>
                        <th>Line</th>
                        <td>$errline</td>
                    </tr>
                    <tr>
                        <th>Trace</th>
                        <td><pre>$trace</pre></td>
                    </tr>
                </tbody>
            </table>";
        return $content;
    }

    protected function getSimple(string $errno, string $errstr, string $errfile, int $errline, int $httpStatusCode): string
    {
        return sprintf(
            "<b>Error Type: %s</b><hr>%s<hr>File: %s<hr> On Line: %s<hr> Status: %s",
            $errno,
            $errstr,
            $errfile,
            $errline,
            $httpStatusCode
        );
    }

    /**
     * @param $errno
     * @param array $loggedError
     */
    private function writeHistoryLog($errno, array $loggedError): void
    {
        switch ($errno) {
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_PARSE:
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                $this->logger->commit($loggedError, "fatal");
                break;
            case E_USER_ERROR:
            case E_RECOVERABLE_ERROR:
                $this->logger->commit($loggedError, "error");
                break;
            case E_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_USER_WARNING:
                $this->logger->commit($loggedError, "warn");
                break;
            case E_NOTICE:
            case E_USER_NOTICE:
                $this->logger->commit($loggedError, "info");
                break;
            case E_STRICT:
                $this->logger->commit($loggedError, "debug");
                break;
            default:
                $this->logger->commit($loggedError, "warn");
        }
    }

    protected function getErrorMessage(): string
    {
        return sprintf("%s<hr> <br><br>", $this->errorMessage);
    }

    protected function getHumanReadableTrace(string $trace = null)
    {
        if (empty($trace)) {
            $trace = print_r(debug_backtrace(false), true);
        }
        $trace = str_replace(',', ",<br>", $trace);
        $trace = str_replace(': ', ":<br>", $trace);
        $trace = str_replace('#', "<br>#", $trace);
        $trace = sprintf("<div style='border: 5px dashed red; padding: 20px;'>%s</div>", $trace);

        return $trace;
    }

    private function displayErrors()
    {
        if (empty($this->errors)) {
            return false;
        }

        foreach ($this->errors as $key => $error) {
            echo $error;
        }
    }

    protected function exceptionHandler(\Throwable $exception): bool
    {
        $this->addError($exception->getCode(), 'Exception: ' . $exception->getMessage(), $exception->getFile(), $exception->getLine());
        return true;
    }
}
