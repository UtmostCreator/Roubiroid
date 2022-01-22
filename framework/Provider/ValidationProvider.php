<?php

namespace Framework\Provider;

use Framework\Application;
use Framework\Rules;
use Framework\validation\rules\DefaultValueRule;
use Framework\validation\rules\EmailRule;
use Framework\validation\rules\InArrayRule;
use Framework\validation\rules\MatchRule;
use Framework\validation\rules\MaxNumberRule;
use Framework\validation\rules\MaxStringRule;
use Framework\validation\rules\MinStringRule;
use Framework\validation\rules\RequiredRule;
use Framework\validation\rules\UniqueRule;
use Framework\validation\ValidationManager;
use Modules\DD;

// TODO add when to params to add a condition (if possible)
class ValidationProvider
{
    protected const RULES = [
        Rules::REQUIRED => RequiredRule::class,
        Rules::EMAIL => EmailRule::class,
        Rules::MIN_STR => MinStringRule::class,
        Rules::MAX_STR => MaxStringRule::class,
        Rules::MIN_NUM => MaxNumberRule::class,
        Rules::MAX_NUM => MaxNumberRule::class,
        Rules::DEFAULT_VALUE => DefaultValueRule::class,
        Rules::MATCH => MatchRule::class,
        Rules::IN_ARRAY => InArrayRule::class,
        Rules::UNIQUE => UniqueRule::class,
        // TODO add: FileRule, ImageRule, PasswordRule, DateRule, ArrayFromStringRule
        // TODO create new: each(will check in array of type or sth), exist(check if attr exist in table)
        // URL validator
        // IP validator
        /* filter (FilterValidator is actually not a validator but a data processor. It invokes the
         specified filter callback to process the attribute value and save the processed value back to
         the attribute. The filter must be a valid PHP callback with the following signature:)*/
        // match to match the pattern (regexp)
        // FilterValidator (trim,filter, integer, float...)

    ];

    public function bind(Application $app): void
    {
        $app->bind('validator', function ($app) {
            $manager = new ValidationManager();

            $this->bindRules($app, $manager);

            return $manager;
        });
    }

    public function bindRules(Application $app, ValidationManager $manager)
    {
        foreach (static::RULES as $ruleName => $ruleClass) {
            $alias = static::getRuleAlias($ruleName);

            $app->bind($alias, fn() => new $ruleClass());
            $manager->addRule($ruleName, $app->resolve($alias));
        }

//        $app->bind('validation.rule.max_string', fn() => new MinStringRule());
//        $manager->addRule(Rules::EMAIL, $app->resolve('validation.rule.email'));
    }

    public static function getRuleAlias($ruleName): string
    {
        return "validation.rule.{$ruleName}";
    }
}
