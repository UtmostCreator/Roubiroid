<?php

namespace app\core\exceptions;

use app\core\Application;
use Throwable;

class NotFoundException extends \Exception
{
    public function __construct($message = "Page Not Found!", $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}