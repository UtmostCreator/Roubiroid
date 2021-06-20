<?php

namespace app\models;

use app\core\Model;

class ContactForm extends Model
{
    public string $email = '';
    public string $subject = '';
    public string $body = '';

    public function rules(): array
    {
        return [
            'email' => [
                self::RULE_REQUIRED, self::RULE_EMAIL,
            ],
            'subject' => [self::RULE_REQUIRED, [self::RULES_MIN, 'min' => 8], [self::RULE_MAX, 'max' => 16]],
            'body' => [self::RULE_REQUIRED, [self::RULES_MIN, 'min' => 8], [self::RULE_MAX, 'max' => 16]],
        ];
    }

    public function send(): bool
    {
        return true;
    }

    public function labels(): array
    {
        return [
            'email' => 'Email Address',
            'subject' => 'Subject',
            'body' => 'Body | Description'
        ];
    }
}