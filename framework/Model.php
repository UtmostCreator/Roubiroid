<?php

namespace Framework;

use Framework\db\Connection\Connection;
use Framework\helpers\ArrayHelper;
use Framework\helpers\Sanitizer;
use Framework\helpers\StringHelper;
use Framework\db\Query;
use Framework\notification\Message;

abstract class Model
{
    public array $errors = [];
    public const SINGLE_ERROR_MESSAGE = false;
    public const RULE_REQUIRED = 'required';

    public const RULES_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULES_MIN_NUM = 'min_num';
    public const RULE_MAX_NUM = 'max_num';
    public const RULE_MATCH = 'match';
    public const RULE_EMAIL = 'email';
    public const RULE_UNIQUE = 'unique';
    public const RULE_IN = 'contains_any_value_in_array';
    public const RULE_ARRAY_FROM_STR = 'array_from_string';
    public const RULE_DEFAULT_VALUE = 'default_value';
    public const RULE_UNIQUE_TOO_SHORT = 'unique_too_short';
    public const MIN_VALUE_TO_CHECK_UNIQUENESS = 3;

    public ?int $id = -1;
    public ?string $scenario = null;

    protected Connection $connection;

    private ?array $skippedFields = [];
    private ?Model $oldObject;
    private array $dirty = [];

    abstract public function rules(): array;

    abstract public static function tableName(): string;

    public function __construct()
    {
        // any action here

        // must be at the end!
        $this->booted();
    }

    public function __get($name)
    {
        try {
            if (property_exists($this, $name)) {
                return $this->{$name};
            }

            $propName = $name;
            $arr = explode('_', $name);
            $arr = array_map(fn($el) => ucfirst($el), $arr);
            $name = implode('', $arr);
            $methodName = "get{$name}Attribute";
            //        dd($methodName);
            if (method_exists($this, $methodName)) {
                return $this->{$methodName}();
            }
            throw new \InvalidArgumentException("There is no such field name like \"{$propName}\"");
        } catch (\Exception $exception) {
            exit($exception->getMessage());
        }
    }

    public function __clone()
    {
        foreach (get_object_vars($this) as $attrKey => $attrVal) {
            $this->id = -1;
            if (!in_array($attrKey, $this->getFillable())) {
                unset($this->{$attrKey});
            }
        }
    }

    public function load($data = [])
    {
        $this->oldObject = clone $this;

//        DD::dd($this->oldObject);
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    protected function booted()
    {
        // it is created and ready for any other secondary tasks.
    }

    public function getFillable(): array
    {
        return [];
    }

    public function validate(): bool
    {
        foreach ($this->rules() as $key => $rules) {
            $attributes = null;
            if (is_string($key)) {
                $attributes = $key;
            } elseif (is_array($rules[0]) && !empty($rules[0])) {
                $attributesArr = array_slice($rules, 0, 1);
                unset($rules[0]);
                $attributes = $attributesArr[0];
            } elseif (is_string($rules[0])) {
                $attributes = $rules[0];
                unset($rules[0]);
            }

            if (method_exists($this, 'scenarios') && isset($this->scenarios()[$this->scenario])) {
                $scenarioFields = $this->scenarios()[$this->scenario];
                if (is_string($attributes) && !in_array($attributes, $scenarioFields)) {
                    $this->skippedFields[$attributes] = true;
                    continue;
                }
                if (is_array($attributes)) {
                    $skipAttrs = array_diff($attributes, $scenarioFields);
                    if (!empty($skipAttrs)) {
                        foreach ($attributes as $attrKey => $attribute) {
                            if (in_array($attribute, $skipAttrs)) {
                                $this->skippedFields[$attribute] = true;
                                // remove attribute from checking
                                unset($attributes[$attrKey]);
                            }
                        }
                    }
                }
            }

            $this->checkIncomingAttributes($attributes);
            if (is_string($attributes)) {
                $this->validateSingleAttribute($attributes, $rules);
            } elseif (is_array($attributes) && !empty($attributes)) {
                foreach ($attributes as $attribute) {
                    $this->validateSingleAttribute($attribute, $rules);
                }
            }
        }
        unset($rules);
        unset($attribute);

        if ($this->hasErrors()) {
//            if ($errorsCount == 1) {
//                $desc = 'The following field is incorrect: ' . HTML::getATag('#' . $fields[0], $fields[0]);
//            } else {
//                $desc = 'The following fields are incorrect: ' . implode(', ', $fields);
//            }
            $attribNames = array_keys($this->errors);
            $fields = array_map(fn($attr) => sprintf("<li>%s</li>", $this->getLabel($attr)), $attribNames);
            $desc = 'The following fields are incorrect:<br /><ul>' . implode("\n", $fields);
            $desc .= "</ul>";
            Application::$app->session->setFlash(
                Message::DANGER,
                'Form Validation',
                $desc,
                Message::ADMIN_VISIBLE
            );
//            DD::dd($_SESSION);
        }
//        DD::dd($this->errors);

        return !$this->hasErrors() && $this->isDirty();
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

//    public function isRequired(string $fieldName)
//    {
//        $rules = $this->rules();
//        if (isset($rules[$fieldName])) {
//            return array_search(self::RULE_REQUIRED, $rules[$fieldName]) !== false;
//        }
//
//        foreach ($rules as $rule) {
//            if (in_array($fieldName, $rule[0]) && in_array('')) {
//
//            }
//        }
//    }

    protected function addError(string $attr, string $message)
    {
        if ($message) {
            $this->errors[$attr][] = $message;
        }
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
            self::RULE_UNIQUE_TOO_SHORT => 'The {field} must have at least {n} chars',
            self::RULE_ARRAY_FROM_STR => 'The {field} must have provide a property to fill',
            self::RULE_IN => 'The {field} must be in {value_list}',
        ];
    }

    public function hasError($fieldName): bool
    {
        if ($this->hasErrors()) {
            return !empty($this->errors[$fieldName]);
        }
        return false;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function getFirstError($fieldName): string
    {
        if ($this->hasError($fieldName)) {
            return $this->errors[$fieldName][0];
        }
        return '';
    }

    public function labels(): array
    {
        return [];
    }

    public function scenarios(): array
    {
        return [];
    }

    public function getLabel($attribute): string
    {
        return $this->labels()[$attribute] ?? StringHelper::uppercaseWordsAndReplaceSpecifier('_', $attribute);
    }

    public static function create(array $mixedKeyValArr, array $valuesArr = [])
    {
        if (ArrayHelper::isAssoc($mixedKeyValArr)) {
            Query::getInst()->insert(static::tableName(), array_keys($mixedKeyValArr), array_values($mixedKeyValArr));
        } elseif (!empty($valuesArr)) {
            Query::getInst()->insert(static::tableName(), $mixedKeyValArr, $valuesArr);
        }
    }

    public static function delete(int $id)
    {
        Query::getInst()->delete(static::tableName(), $id);
    }

    public static function deleteWhereIn($field, array $arr): int
    {
        return Query::getInst()->deleteWhereIn(static::tableName(), $field, $arr);
    }

    public static function update($data, $where): int
    {
        return Query::getInst()->update(static::tableName(), $data, $where);
    }

    private function validateSingleAttribute(string $attr, array $rules)
    {
        $value = $this->{$attr};
        foreach ($rules as $rule) {
            $ruleName = $rule; // e.g. self::RULE_REQUIRED, self::RULE_EMAIL
            if (!is_string($ruleName)) {
                /** e.g.
                 * [self::RULE_UNIQUE, 'class' => self::class] */
                $ruleName = is_string(array_key_first($rule)) ? array_key_first($rule) : array_shift($rule);
            }

            // exit if singleError = true and there is an error for this field
            if ($this->hasError($attr) && self::SINGLE_ERROR_MESSAGE) {
                continue;
            }

            // rule
            // $attr - attribute, field name;
            // $value - value to be validated
            // TODO convert all int to int
//            DD::dd($rule);
            $ruleValue = $rule[$ruleName] ?? null;
//            DD::dd($ruleName);
            switch ($ruleName) {
                case self::RULE_REQUIRED:
                    if (!$value && !($value === 0)) {
                        $this->addErrorForRule($attr, $ruleName);
                    }
                    break;
                case self::RULES_MIN:
                    $this->{$attr} = filter_var($value, FILTER_SANITIZE_STRING);
                    if (strlen($value) < $ruleValue) {
                        $this->addErrorForRule($attr, $ruleName, $rule);
                    }
                    break;
                case self::RULE_MAX:
//                    $n = strip_tags(html_entity_decode($value));
                    // TODO check for all strings
                    $this->{$attr} = strip_tags(html_entity_decode($value));
//                    DD::dd($value);
                    if (strlen($value) > $ruleValue) {
                        $this->addErrorForRule($attr, $ruleName, $rule);
                    }
                    break;
                case self::RULES_MIN_NUM:
                    if ($value < $ruleValue) {
                        $this->addErrorForRule($attr, $ruleName, $rule);
                    }
                    break;
                case self::RULE_MAX_NUM:
                    if ($value > $ruleValue) {
                        $this->addErrorForRule($attr, $ruleName, $rule);
                    }
                    break;
                case self::RULE_ARRAY_FROM_STR:
                    if (!is_string($value)) {
                        throw new \InvalidArgumentException('must be a string!');
                    }
                    if (!isset($rule['separated']) || !isset($rule['fill'])) {
                        throw new \InvalidArgumentException('self::RULE_SEPARATEVALUE_WITH must set the "separated" and "fill"');
                    }

                    if (empty($value)) {
                        break;
                    }

                    $value = Sanitizer::string($value);

                    $pos = ArrayHelper::strposa($this->{$attr}, $rule['separated']);
//                    DD::dd($pos);
                    if ($pos >= 0) {
                        $this->{$rule['fill']} = explode($rule['separated'][$pos], $value);
                    }
                    break;
                // TODO add compare > >= < <= = <> (!=)
                // TODO date
                case self::RULE_MATCH:
                    $compareWith = $ruleValue;
                    $ruleValue = $this->getLabel($compareWith);
                    if ($value !== $this->{$compareWith}) {
                        $this->addErrorForRule($attr, $ruleName, $rule);
                    }
                    break;
                case self::RULE_EMAIL:
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $this->addErrorForRule($attr, $ruleName);
                    }
                    break;
                case self::RULE_DEFAULT_VALUE:
                    $compareWith = $ruleValue;
                    if (!($value || $compareWith) && !($value == 0 && $compareWith == 0)) {
                        $this->addErrorForRule($attr, $ruleName);
                    }

                    if (!empty($value) || $value === 0) {
                        $this->{$attr} = $value;
                    } else {
                        $this->{$attr} = $compareWith;
                    }
                    break;
                case self::RULE_IN:
                    if (is_array($ruleValue) || empty($ruleValue)) {
                        // TODO CORE_ERROR [output message only for developers]
                        throw new \InvalidArgumentException('self::RULE_IN requires an array of values');
                    }
                    if (in_array($value, $ruleValue)) {
                        $this->addErrorForRule($attr, $ruleName, [
                            'field' => $this->getLabel($attr),
                            'value_list' => implode(', ', $ruleValue)
                        ]);
                    }
                    break;
                case self::RULE_UNIQUE:
                    if (strlen($value) <= self::MIN_VALUE_TO_CHECK_UNIQUENESS) {
                        $this->addErrorForRule($attr, $ruleName, [
                            'field' => $this->getLabel($attr),
                            'n' => self::MIN_VALUE_TO_CHECK_UNIQUENESS
                        ]);
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
                        $this->addErrorForRule($attr, $ruleName, ['field' => $this->getLabel($attr)]);
                    }
                    break;
                default:
                    $this->addErrorForRule($attr, 'We Have Encountered to an Unknown Validator');
                    break;
            }
        }
    }

    /**
     * @param $attributes
     */
    private function checkIncomingAttributes($attributes): void
    {
        if (
            !(isset($attributes) && is_string($attributes)) &&
            !(isset($attributes) && is_array($attributes))
        ) {
            throw new \InvalidArgumentException("You must use either an array of attributes or a single attribute name");
        }
    }

    public function addDirty($attrName)
    {
        $this->dirty[] = $attrName;
    }

//    public function markDirty()
//    {
//        ObjectWatcher::addDirty();
//    }

    private function isDirty(): bool
    {
        foreach ($this->oldObject as $attrName => $attrValue) {
            if (in_array($attrName, $this->getFillable()) && $this->oldObject->{$attrName} != $this->{$attrName}) {
                $this->addDirty($attrName);
            }
        }

        if (empty($this->dirty)) {
            Application::app()->session->setFlash(Message::WARNING, 'Form Validation', 'No fields were changed');
        }
//        DD::dd($this->dirty);
        return count($this->dirty) > 0;
    }
}
