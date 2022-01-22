<?php

namespace Framework\helpers;

use Modules\DD;

class UtilsHelper
{
    public static function isEmpty($data): bool
    {
        if (is_array($data) && empty($data)) {
            return true;
        }
        if (in_array($data, [0.0, "0", 0], true)) {
            return false;
        }
        return empty($data);
    }
}
