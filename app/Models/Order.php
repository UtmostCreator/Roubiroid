<?php

namespace App\Models;

use Framework\db\BaseActiveRecord;

class Order extends BaseActiveRecord
{
    protected string $table = 'orders';

    public function user()
    {
        return $this->belongsTo(NewUser::class, 'user_id');
    }
}
