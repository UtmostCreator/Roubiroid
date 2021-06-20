<?php

namespace app\core\exceptions;

use app\core\Application;
use Throwable;

class NotFoundException extends \Exception
{
    protected $code = 404;
    protected $message = 'Page Not Found!';

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}