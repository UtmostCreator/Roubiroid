<?php

namespace models;

use App\Models\NewUser;
use Framework\db\BaseActiveRecord;

class Profile extends BaseActiveRecord
{
    protected string $table = 'profile';

    public function user()
    {
        return $this->belongsTo(NewUser::class, 'user_id');
    }
}