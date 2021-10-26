<?php

namespace models;

use App\Models\NewUser;
use Framework\db\NewModel;

class Profile extends NewModel
{
    protected string $table = 'profile';

    public function user()
    {
        return $this->belongsTo(NewUser::class, 'user_id');
    }
}