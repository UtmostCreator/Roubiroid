<?php

namespace models;

use Framework\Application;
use Framework\Model;
use Framework\Rules;
use Modules\DD;

class LoginForm extends Model
{
    protected $validationGeneralDescription = 'Виправте помилки та повторіть спробу';
    protected $validationTitle = 'Дані введені некоректно';
    protected array $fillable = ['email', 'password'];

    public function rules(): array
    {
        return [
            'email' => Rules::EMAIL,
            [['email'], Rules::MIN_STR, Rules::MIN_STR => 3, 'message' => 'Вкажіть як мінімум 3 символів'],
            [['password'], Rules::MIN_STR, Rules::MIN_STR => 8, 'message' => 'Вкажіть як мінімум 8 символів'],
        ];
    }

    public function login(): bool
    {
        $user = User::where('email', '=', $this->email)->first();
        if (!$user) {
            $this->addError('email', 'Електронна Адреса або пароль введені некоректно');
            return false;
        }

        if (!password_verify($this->password, $user->password)) {
            $this->addError('password', 'Електронна Адреса або пароль введені некоректно');
            return false;
        }

        return Application::$app->login($user);
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