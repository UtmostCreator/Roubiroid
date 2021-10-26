<?php

namespace App\Models;

use Framework\db\NewModel;

class Order extends NewModel
{
    protected string $table = 'orders';

    public function user()
    {
        return $this->belongsTo(NewUser::class, 'user_id');
    }
}
