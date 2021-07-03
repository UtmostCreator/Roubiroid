<?php

namespace app\core\permission_roubiroid;

use app\core\Model;

class Permission extends Model implements InterfacePermission
{
    public function rules(): array
    {
        return [];
    }

    public static function tableName(): string
    {
        return 'permissions';
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
