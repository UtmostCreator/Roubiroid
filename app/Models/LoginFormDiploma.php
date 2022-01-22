<?php

namespace models;

use Framework\Application;
use Framework\Model;
use Framework\Rules;
use Framework\Session;
use Modules\DD;

class LoginForm extends Model
{
    protected array $fillable = ['email', 'password'];

    public function rules(): array
    {
        return [
            'email' => Rules::EMAIL,
            [['email', 'password'], Rules::REQUIRED],
            [['email'], Rules::MIN_STR, Rules::MIN_STR => 3],
            [['password'], Rules::MIN_STR, Rules::MIN_STR => 8],
        ];
    }

    public function login(): bool
    {
        $user = User::where('email', '=', $this->email)->first();
        if (!$user) {
            $this->addError('email', 'Електронна Адреса чи пароль не вірні');
            return false;
        }

        if (!password_verify($this->password, $user->password)) {
            $this->addError('password', 'Електронна Адреса чи пароль не вірні');
            return false;
        }

        return Session::set('user', $user::primaryKey());
    }

    public function labels(): array
    {
        return [
            'email' => 'Електронна Адреса',
            'password' => 'Пароль'
        ];
    }

    public static function tableName(): string
    {
        // TODO: Implement tableName() method.
    }

    protected function scenarios(): array
    {
        // TODO: Implement scenarios() method.
    }
}