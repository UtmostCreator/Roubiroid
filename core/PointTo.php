<?php

namespace app\core;

class PointTo
{

    public static function views($path): string
    {
        return '../views/' . $path;
    }

}