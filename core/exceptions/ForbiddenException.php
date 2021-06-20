<?php

namespace app\core\exceptions;

use app\core\Application;
use Throwable;

class ForbiddenException extends \Exception
{
//    protected $message = 'You do not have permissions to access this page';
    protected $message = 'Insufficient rights to access it';
    protected $code = 403;

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $this->message = $message ? $message : $this->message;
        $this->code = $code ? $code : $this->code;
        parent::__construct($this->message, $code, $previous);
    }
}