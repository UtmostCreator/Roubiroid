<?php

namespace app\core\permission_roubiroid;

use app\core\Model;

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