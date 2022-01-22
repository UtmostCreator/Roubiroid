<?php

namespace models;

use Framework\Model;

class User extends Model
{
    public string $table = 'users';
    public array $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
    ];


    public static function primaryKey(): string
    {
        return 'id';
    }
}
