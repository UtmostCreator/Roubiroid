<?php

namespace Framework\db\exception;

class QueryException extends \PDOException
{
    public function __construct(string $msg) //  int $code, string $file, int $line
    {
        parent::__construct($msg);
    }
}
