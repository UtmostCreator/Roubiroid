<?php

namespace Framework\permission_roubiroid;

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

    public static function attributes(): array
    {
        return [
            'name',
            'guard_name',
            'created_at',
            'updated_at'
        ];
    }

}