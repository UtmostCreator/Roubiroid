<?php

namespace App\Models;

use Framework\db\NewModel;
use models\Profile;

// #[Table('users')] // TODO PHP 8 only
class NewUser extends NewModel
{
    public string $table = 'users';

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }
}
