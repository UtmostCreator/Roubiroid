<?php

// class only for PHP 8.0+
#[Attribute]
class TableName
{
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

}