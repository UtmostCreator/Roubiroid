<?php

namespace Framework\routing\rbac;

use Framework\Application;
use Framework\db\Query;

class RbacManager
{
    public const ROLES_TABLE = 'roles';
    public const PERMISSION_TABLE = 'permissions';
    public const _TABLE = 'permissions';
    public array $assignments = [];
    public array $children = [];
    public array $defaultRoles = [];
    public array $items = [];
    public array $rules = [];

    public const PERMISSION = 'permission';
    public const ROLE = 'role';

    public Query $q;

    public function __construct()
    {
        $this->q = new Query();
    }

    public function add(string $type, $name)
    {
        switch ($type) {
            case self::PERMISSION:

                break;
            case self::ROLE:

                break;
        }
    }

    public function createRole($name)
    {
        $this->q->insert(self::ROLES_TABLE, ['attribs'], ['values']);
    }

    public function checkAccess($userId, $permissionName)
    {
        
    }
}