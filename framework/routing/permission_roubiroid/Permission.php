<?php

namespace Framework\routing\permission_roubiroid;

use Framework\Model;

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
