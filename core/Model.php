<?php

namespace app\core;

use app\common\helpers\StringHelper;
use app\core\Application;
use app\core\db\Query;
use modules\DD\DD;

abstract class Model
{
    public array $errors = [];
    public bool $allowSingleErrorMessage = false;
    public const RULE_REQUIRED = 'required';

    public const RULES_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULES_MIN_NUM = 'min_num';
    public const RULE_MAX_NUM = 'max_num';
    public const RULE_MATCH = 'match';
    public const RULE_EMAIL = 'email';
    public const RULE_UNIQUE = 'unique';
    public const MIN_VALUE_TO_CHECK_UNIQUENESS = 3;

    abstract public function rules(): array;

    public function load($data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public function validate(): bool
    {
        foreach ($this->rules() as $attr => $rules) {
            $value = $this->{$attr};
            foreach ($rules as $rule) {
                $ruleName = $rule; // e.g. self::RULE_REQUIRED, self::RULE_EMAIL
                if (!is_string($ruleName)) {
                    /** e.g.
                    [self::RULE_UNIQUE, 'class' => self::class] */
                    $ruleName = $rule[0];
                }

                // exit if singleError = true and there is an error for this field
                if (!empty($this->errors) && $this->allowSingleErrorMessage) {
                    continue;
                }

                // rule
                // $attr - attribute, field name;
                // $value - value to be validated
                switch ($ruleName) {
                    case self::RULE_REQUIRED:
                        !$value ? $this->addErrorForRule($attr, self::RULE_REQUIRED) : null;
                        break;
                    case self::RULES_MIN:
                        if (strlen($value) < $rule[self::RULES_MIN]) {
                            $this->addErrorForRule($attr, self::RULES_MIN, $rule);
                        }
                        break;
                    case self::RULE_MAX:
                        if (strlen($value) > $rule[self::RULE_MAX]) {
                            $this->addErrorForRule($attr, self::RULE_MAX, $rule);
                        }
                        break;
                    case self::RULES_MIN_NUM:
                        if ($value < $rule[self::RULES_MIN_NUM]) {
                            $this->addErrorForRule($attr, self::RULES_MIN_NUM, $rule);
                        }
                        break;
                    case self::RULE_MAX_NUM:
                        if ($value > $rule[self::RULE_MAX_NUM]) {
                            $this->addErrorForRule($attr, self::RULE_MAX_NUM, $rule);
                        }
                        break;
                    case self::RULE_MATCH:
                        $compareWith = $rule[self::RULE_MATCH];
                        $rule[self::RULE_MATCH] = $this->getLabel($compareWith);
                        if ($value !== $this->{$compareWith}) {
                            $this->addErrorForRule($attr, self::RULE_MATCH, $rule);
                        }
                        break;
                    case self::RULE_EMAIL:
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $this->addErrorForRule($attr, self::RULE_EMAIL);
                        }
                        break;
                    case self::RULE_UNIQUE:
                        if (strlen($value) <= self::MIN_VALUE_TO_CHECK_UNIQUENESS) {
                            continue 2;
                        }
                        $className = $rule['class'];
                        $uniqueAttr = $rule['attribute'] ?? $attr;
                        $tableName = $className::tableName();

//                        DD::dd([$uniqueAttr => $value]);
                        $record = (new Query())
                            ->select($uniqueAttr)
                            ->from($tableName)
                            ->where([$uniqueAttr => $value])
//                            ->andWhere(['firstname' => 'Roman'])
                            ->one();
                        if ($record) {
                            $this->addErrorForRule($attr, self::RULE_UNIQUE, ['field' => $this->getLabel($attr)]);
                        }
                        break;
                    default:
                        $this->addErrorForRule($attr, 'We Have Encountered to an Unknown Validator');
                        break;
                }
            }
        }

        return empty($this->errors);
    }

    protected function addErrorForRule(string $attr, string $ruleName, $params = [])
    {
        // get the message for the specific Rule
        $message = $this->errorMessages()[$ruleName] ?? '';
        foreach ($params as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attr][] = $message;
    }

    protected function addError(string $attr, string $message)
    {
        $this->errors[$attr][] = $message;
    }

    public function errorMessages(): array
    {
        return [
            self::RULE_REQUIRED => 'This field is required',
            self::RULE_EMAIL => 'This field must be a valid email address',
            self::RULES_MIN => 'This field must contain at least {min} chars',
            self::RULE_MAX => 'This field must contain at most {max} chars',
            self::RULE_MATCH => 'This field  must be same as {match}',
            self::RULE_UNIQUE => 'Record with this {field} already exists',
        ];
    }

    public function hasError($fieldName): bool
    {
        return !empty($this->errors[$fieldName]);
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function getFirstError($fieldName): string
    {
        if (!$this->hasErrors() || empty($this->errors[$fieldName])) {
            return '';
        }

        if (is_array($this->errors[$fieldName]) && !empty($this->errors[$fieldName])) {
            return $this->errors[$fieldName][0];
        }

        return $this->errors[$fieldName][0];
    }

    public function labels(): array
    {
        return [];
    }

    public function getLabel($attribute): string
    {
        return $this->labels()[$attribute] ?? StringHelper::uppercaseWordsAndReplaceSpecifier('_', $attribute);
    }
}
