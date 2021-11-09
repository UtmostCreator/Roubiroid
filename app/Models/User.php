<?php

namespace models;

use App\Models\Order;
use Framework\routing\permission_roubiroid\HasRoles;
use Framework\UserModel;

class User extends UserModel
{
    use HasRoles;

    public string $table = 'users';

    protected $guard_name = 'web';

    public const INACTIVE_STATUS = 0;
    public const ACTIVE_STATUS = 1;
    public const DELETED_STATUS = 2;
    public const ROLE_ADMIN = 1;
    public const ROLE_EDITOR = 2;
    public const ROLE_USER = 3;
    public const ROLE_GUEST = 0;

//    public string $table = 'users';
//    public string $firstname = '';
//    public string $lastname = '';
//    public string $email = '';
//    public string $password = '';
//    public int $status = self::ACTIVE_STATUS;
//    public string $confirmPassword = '';

    /**
     * RegisterModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
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
//             ['firstname', 'lastname', [self::RULE_REQUIRED]],
//            'lastname' => [self::RULE_REQUIRED],
            'email' => [

                [
                    self::RULE_UNIQUE, 'class' => self::class
                ]
            ],
            'password' => [self::RULE_REQUIRED, [self::RULES_MIN, 'min' => 8], [self::RULE_MAX, 'max' => 16]],
            'confirmPassword' => [self::RULE_REQUIRED, [self::RULE_MATCH, 'match' => 'password']],
            'status' => [self::RULE_DEFAULT_VALUE => 1],
        ];
    }

    // TODO remove
//    public static function attributes(): array
//    {
//        return [
//            'firstname',
//            'lastname',
//            'email',
//            'password',
//            'status',
//        ];
//    }

    public function getFillable(): array
    {
        return [
            'firstname',
            'lastname',
            'email',
            'password',
            'status',
        ];
    }

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function test()
    {
        echo 'TEST in f';
    }

    public function save(): self
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

    public function getIsAdminAttribute(): string
    {
        return 'You are an admin user!';
    }
}
