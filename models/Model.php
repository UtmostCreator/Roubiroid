<?php


namespace app\models;


use app\core\Application;

abstract class Model
{
    public array $errors = [];
    public bool $singleError = false;

    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULES_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'match';

    abstract public function rules(): array;

    public function load($data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public function validate()
    {
        foreach ($this->rules() as $attr => $rules) {
            $value = $this->{$attr};
            foreach ($rules as $rule) {
                $ruleName = $rule;
                if (!is_string($ruleName)) {
                    $ruleName = $rule[0];
                }

                // exit if singleError = true and there is an error for this field
                if (!empty($this->errors) && $this->singleError){
                    continue;
                }

                switch ($ruleName) {
                    case self::RULE_REQUIRED:
                        !$value ? $this->addError($attr, self::RULE_REQUIRED) : null;
                        break;
                    case self::RULE_EMAIL:
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $this->addError($attr, self::RULE_EMAIL);
                        }
                        break;
                    case self::RULES_MIN:
                        if (strlen($value) < $rule[self::RULES_MIN]) {
                            $this->addError($attr, self::RULES_MIN, $rule);
                        }
                        break;
                    case self::RULE_MAX:
                        if (strlen($value) > $rule[self::RULE_MAX]) {
                            $this->addError($attr, self::RULE_MAX, $rule);
                        }
                        break;
                    case self::RULE_MATCH:
                        $compareWith = $rule[self::RULE_MATCH];
                        if ($value !== $this->{$compareWith}) {
                            $this->addError($attr, self::RULE_MATCH, $rule);
                        }
                        break;
                    default:
                        $this->addError($attr, 'We Have Encountered to an Unknown Validator');
                        break;
                }
            }
        }

        return empty($this->errors);
    }

    private function addError(string $attr, string $ruleName, $params = [])
    {
        $message = $this->errorMessages()[$ruleName] ?? '';
        foreach ($params as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attr][] = $message;
    }

    public function errorMessages() {
        return [
            self::RULE_REQUIRED => 'This field is required',
            self::RULE_EMAIL => 'This field must be a valid email address',
            self::RULES_MIN => 'This field must contain at least {min} chars',
            self::RULE_MAX => 'This field must contain at most {max} chars',
            self::RULE_MATCH => 'This field  must be same as {match}',
        ];
    }

}
