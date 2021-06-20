<?php

namespace app\models;

use app\core\Application;
use app\core\Model;

class LoginForm extends Model
{
    public string $email = '';
    public string $password = '';

    public function rules(): array
    {
        return [
            'email' => [
                self::RULE_REQUIRED, self::RULE_EMAIL,
            ],
            'password' => [self::RULE_REQUIRED, [self::RULES_MIN, 'min' => 8], [self::RULE_MAX, 'max' => 16]],
        ];
    }

    public function login(): bool
    {
        $user = User::findOne(['email' => $this->email]);
        if (!$user) {
            $this->addError('email', 'Check if you enter you data correctly!');
            return false;
        }

        if (!password_verify($this->password, $user->password)) {
            $this->addError('password', 'Password is incorrect');
            return false;
        }

        return Application::$app->login($user);
    }

    public function labels(): array
    {
        return [
            'email' => 'Email Address',
            'password' => 'Password'
        ];
    }
}