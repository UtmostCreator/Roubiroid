<?php

namespace Framework\exceptions;

use Exception;
use Throwable;

class CustomException extends Exception
{
    private array $_options;

    public function __construct(
        $message,
        $code = 500,
        Throwable $previous = null,
        $options = array('params')
    )
    {
        http_response_code($code);
        parent::__construct($message, $code, $previous);

        $this->_options = $options;
    }

    public function getOptions(): array
    {
        return $this->_options;
    }

    public function getErrorMsg(): string
    {
        return $this->getMessage();
    }
}
