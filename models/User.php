<?php

namespace app\models;

use app\core\db\DbModel;
use app\core\db\Query;
use app\core\Model;
use app\core\UserModel;
use modules\DD\DD;

class User extends UserModel
{
    public const INACTIVE_STATUS = 0;
    public const ACTIVE_STATUS = 1;
    public const DELETED_STATUS = 2;
    public const ROLE_ADMIN = 1;
    public const ROLE_EDITOR = 2;
    public const ROLE_USER = 3;
    public const ROLE_GUEST = 0;

    public string $firstname = '';
    public string $lastname = '';
    public string $email = '';
    public string $password = '';
    public int $status = self::ACTIVE_STATUS;
    public string $confirmPassword = '';

    /**
     * RegisterModel constructor.
     */
    public function __construct()
    {
    }

    public static function tableName(): string
    {
        return 'users';
    }

    public static function primaryKey(): string
    {
        return 'id';
    }

    public function rules(): array
    {
        return [
            'firstname' => [self::RULE_REQUIRED],
            'lastname' => [self::RULE_REQUIRED],
            'email' => [
                self::RULE_REQUIRED, self::RULE_EMAIL,
                [
                    self::RULE_UNIQUE, 'class' => self::class
                ]
            ],
            'password' => [self::RULE_REQUIRED, [self::RULES_MIN, 'min' => 8], [self::RULE_MAX, 'max' => 16]],
            'confirmPassword' => [self::RULE_REQUIRED, [self::RULE_MATCH, 'match' => 'password']],
        ];
    }

    public static function attributes(): array
    {
        return [
            'firstname',
            'lastname',
            'email',
            'password',
            'status',
        ];
    }

    public function test()
    {
        echo 'TEST in f';
    }

    public function save(): bool
    {
        $pass = password_hash($this->password, PASSWORD_DEFAULT);
        $this->password = is_string($pass) ? $pass : $this->password;
        // TODO if !is_string($pass) then log error;
        return parent::save();
    }

    public function labels(): array
    {
        return [
            'firstname' => 'First Name',
            'lastname' => 'Last Name',
            'email' => 'Email Address',
            'password' => 'Password',
            'confirmPassword' => 'Confirm Password',
            'status' => 'Status',
        ];
    }

//    public function isEmailTaken()
//    {
//        $res = (new Query())
//            ->select()
//            ->from(User::tableName())
//            ->where('email = :email', ['email' => $this->email])
////            ->andWhere('email = :email', ['email' => 'romazahrypa1@gmail.com'])
////            ->andWhere('firstname = :firstname', ['firstname' => 'Roman1'])
//            ->all();
////        DD::dd($this->email);
//        return !empty($res);
//    }
    public function getDisplayName()
    {
        return "$this->firstname $this->lastname";
    }

    public function getUserRole()
    {
        return 1;
    }
}
