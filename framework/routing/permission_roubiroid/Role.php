<?php

namespace Framework\routing\permission_roubiroid;

use Framework\Model;

class Role extends Model implements InterfaceRole
{

    public function rules(): array
    {
        return [];
    }

    public static function tableName(): string
    {
        return 'roles';
    }

    public static function getAttributes(): array
    {
        return [
            'name',
            'guard_name',
            'created_at',
            'updated_at'
        ];
    }

}