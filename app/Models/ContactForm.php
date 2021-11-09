<?php

namespace models;

use Framework\Model;

class ContactForm extends Model
{
    public const SCENARIO_EMAIL = 'test';
    public const SCENARIO_VISIBLE_ONLY = 'visible_only';
    public string $email = '';
    public string $subject = '';
    public string $body = '';
    public string $test_prop = '';
    public array $arr = [];

    public function rules(): array
    {
        return [
            'subject' => [[self::RULE_ARRAY_FROM_STR, 'separated' => [';', ' '], 'fill' => 'arr'], self::RULE_REQUIRED],
//            'email' => [[self::DEFAULT_VALUE => 'email']],
//            'email' => [5 => [self::RULE_IN => ['test@gmail.com', 'test1@gmail.com']], self::RULE_EMAIL],
//            ['email', self::RULE_REQUIRED, self::RULE_EMAIL],
//            'email' => [5 => self::RULE_REQUIRED, self::RULE_EMAIL],
            [['email', 'test_prop'], self::RULE_REQUIRED, self::RULE_EMAIL],
//            [['email'], self::RULE_REQUIRED, self::RULE_EMAIL],
//            [['subject', 'body'], self::RULE_REQUIRED, [self::RULES_MIN => 8], [self::RULE_MAX => 16]],
        ];
    }

    public function scenarios(): array
    {

        /*      @TODO in case you need parent scenario
         * $scenarios = parent::scenarios();
         * $scenarios[self::SCENARIO_LOGIN] = ['logonname', 'password'];
         * return $scenarios*/
//        return [self::SCENARIO_LOGIN => ['logonname', 'password'], self::SCENARIO_REGISTER => ['logonname', 'password'], self::SCENARIO_EDIT => ['logonname', 'role'], // , 'password'
//        ];
        return [
            self::SCENARIO_VISIBLE_ONLY => ['email', 'subject', 'email'], // 'test_prop'
            self::SCENARIO_EMAIL => ['email'], // 'test_prop'
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
            'body' => 'Body | Description',
            'test_prop' => 'Test Property',
        ];
    }

    public static function tableName(): string
    {
        // TODO: Implement tableName() method.
    }

    public function getFillable(): array
    {
        return [
            'email',
            'subject',
            'body',
            'test_prop'
        ];
    }
}