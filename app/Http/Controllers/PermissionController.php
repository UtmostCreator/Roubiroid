<?php


namespace app\http\controllers;


use app\core\Controller;
use app\core\permission_roubiroid\Permission;
use app\core\permission_roubiroid\Role;

class PermissionController extends Controller
{
    public function createPermissions()
    {
//        dd(time());
//        Role::create(Permission::attributes(), ['user', 'web', date("Y-m-d H:i:s"), date("Y-m-d H:i:s")]);
//        dd(Role::deleteWhereIn('id', [4,5]));
//        dd(Role::update(['name' => 'uuuuuuuuuu', 'guard_name' => 'web11111111'], ['name' => 'user', 'guard_name' => 'web']));

        return $this->render('create-permissions');
    }

    public function createRoles()
    {
//        dd(time());
//        Role::create(Role::attributes(), ['user', 'web', date("Y-m-d H:i:s"), date("Y-m-d H:i:s")]);
//        dd(Role::deleteWhereIn('id', [4,5]));

        return $this->render('create-permissions');
    }
}