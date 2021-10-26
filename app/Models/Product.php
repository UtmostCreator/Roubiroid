<?php

namespace models;

use Framework\db\NewModel;

class Product extends NewModel
{
    protected string $table = 'products';

    //
    protected array $casts = [
        'id' => 'Framework\db\toInt',
    ];

    public function getNameAttribute($value): string
    {
        return ucwords($value);
    }

    /** What about if we want to allow Product to define custom getters and setters? Let’s
    define a name getter that starts each word with an uppercase letter and a description
    setter that limits the number of description characters to 50: */
    public function setDescriptionAttribute(string $value)
    {
        $limit = 50;
        $ending = '...';

        if (mb_strwidth($value, 'UTF-8') <= $limit) {
            return $value;
        }

        return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')) . $ending;
    }
}
