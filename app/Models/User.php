<?php

namespace models;

use App\Models\Order;
use Framework\Model;
use Framework\routing\permission_roubiroid\HasRoles;
use Framework\Rules;
use Framework\UserModel;
use Framework\validation\rules\RequiredRule;
use Modules\DD;

class User extends UserModel
{
//    use HasRoles;

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
//            'firstname' => Rules::REQUIRED,
//            'lastname' => [Rules::REQUIRED],
//            'firstname' => [Rules::REQUIRED, 'message' => 'Test message'],
//            ['firstname', 'required', 'message' => 'text'],
//            [['firstname', 'lastname'], 'required'],
//            [['firstname', 'lastname'], 'required', 'message' => 'sdfasdlfjk', 'skipOnEmpty' => true, 'sadf' => 3],
//            'email' => [Rules::EMAIL],
//            'email' => [Rules::EMAIL, 'message' => 'test email wrong'],
//            [['firstname'], ['image', 'minFileSize' => 1 * 1024, 'maxFileSize' => 1900 * 1024, 'skipOnEmpty' => true, 'message' => 'asdfs']],
//            ['firstname', ['image', 'minFileSize' => 1 * 1024, 'maxFileSize' => 1900 * 1024, 'skipOnEmpty' => true, 'message' => 'asdfs']],
//            'firstname' => ['image', 'minFileSize' => 1 * 1024, 'maxFileSize' => 1900 * 1024, 'skipOnEmpty' => true, 'message' => 'asdfs'],
//            'image' => ['image', 'minFileSize' => 1 * 1024, 'maxFileSize' => 1900 * 1024, 'skipOnEmpty' => true],
//            'customValidator' => CustomValidator::class,
//            'firstname' => 'validatorImplementedInModel',
//            'firstname' => RequiredRule::class,
//            'customValidator' => 'validatorImplementedInModel',
//             ['firstname', 'lastname', [[Rules::REQUIRED, 'message' => 'Custom Required Message'], Rules::DEFAULT_VALUE => 1]],
//             [['firstname', 'lastname'], ['default', 'value' => 1]],
//             ['firstname', 'lastname', 'email', [Rules::REQUIRED]],
//             [['firstname', 'lastname', 'password'], [Rules::REQUIRED]],
//             [['firstname'], [Rules::REQUIRED, 'message' => 'EDITED message in rules'], 'min_str:3'],
//            'lastname' => [Rules::REQUIRED],
//            'email' => 'email|required|defaultValue=0|min_str:3|max_str:10&msg=Some Error Msg goes here',
//            'email' => [
//
//                [
//                    Rules::UNIQUE, 'class' => self::class
//                ]
//            ],
                'email' => 'unique'
//            'password' => [Rules::REQUIRED, 'min_str:3', 'max_str:10'],
//            'confirmPassword' => [Rules::REQUIRED, 'min_str' => 3, 'max_str:10'],
//            'confirmPassword' => [Rules::MATCH, 'value' => 'password'],
//            'firstname' => ['in_array', 'arr' => ['test', 'first', 'email']],
//            'password' => [Rules::MATCH, 'value' => 'confirmPassword'],
//            'confirmPassword' => [Rules::MATCH => 'password'],
//            'confirmPassword' => [Rules::REQUIRED, [Rules::MATCH, 'match' => 'password']],
//            'status' => [Rules::DEFAULT_VALUE => 1],
//            ['firstname', 'required'],
        ];
    }

    // TODO remove
//    public static function getAttributes(): array
//    {
//        return [
//            'firstname',
//            'lastname',
//            'email',
//            'password',
//            'status',
//        ];
//    }

//    public function validatorImplementedInModel(string $field, array $params)
////    public function validatorImplementedInModel(Model $model, string $field, array $params)
//    {
////        DD::dl($model);
//        DD::dl($field);
//        DD::dl($params);
//        DD::dd('string $field, array $params');
//    }

    protected function scenarios(): array
    {
        return [
            static::SCENARIO_EDIT => [...$this->getFillable(), 'confirmPassword']
        ];
    }

    public function getFillable(): array
    {
        $this->fillable = [
            'name',
            'email',
            'password',
        ];
//        $this->fillable = [
//            'firstname',
//            'lastname',
//            'email',
//            'password',
//            'status',
//        ];
        return $this->fillable;
    }

    public static function messages()
    {
        return [
            'attribute.rule' => 'Your custom message',
            'firstname.required' => 'Firstname is required',
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
