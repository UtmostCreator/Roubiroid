<?php

namespace Framework;

use Framework\db\BaseActiveRecord;
use Framework\db\Connection\Connection;
use Framework\db\Query;
use Framework\helpers\ArrayHelper;
use Framework\helpers\StringHelper;
use Framework\helpers\UtilsHelper;
use Framework\notification\Message;
use Modules\DD;

abstract class Model extends BaseActiveRecord
{
    public const SCENARIO_NEW = 'new';
    public const SCENARIO_EDIT = 'edit';
    public const SCENARIO_DELETE = 'delete';

    public array $errors = [];
// TODO remove or add [but!!! consider that validators also may change values not only validate it]
    public const SINGLE_ERROR_MESSAGE = false;

    public ?string $scenario = null;
    protected ?int $id = -1;
    protected array $fillable = [];

    protected Connection $connection;

    private ?array $skippedFields = [];
    private ?Model $oldObject;
    /**
     * @var mixed|null
     */
    protected $checkIfHasChanges = true;
    /**
     * @var string
     */
    protected $validationGeneralDescription = 'The following fields are incorrect';
    /**
     * @var string
     */
    protected $validationTitle = 'Form Validation';

    abstract public function rules(): array;

    abstract public static function tableName(): string;

    public function __construct()
    {
        // any action here

        // must be at the end!
        $this->booted();
    }

//    public function __get($name)
//    {
//        try {
//            if (property_exists($this, $name)) {
//                return $this->{$name};
//            }
//
//            $propName = $name;
//            $arr = explode('_', $name);
//            $arr = array_map(fn($el) => ucfirst($el), $arr);
//            $name = implode('', $arr);
//            $methodName = "get{$name}Attribute";
//            //        dd($methodName);
//            if (method_exists($this, $methodName)) {
//                return $this->{$methodName}();
//            }
//            throw new \InvalidArgumentException("There is no such field name like \"{$propName}\"");
//        } catch (\Exception $exception) {
//            exit($exception->getMessage());
//        }
//    }

//    public function __clone()
//    {
//        foreach (get_object_vars($this) as $attrKey => $attrVal) {
//            $this->id = -1;
//            if (!in_array($attrKey, $this->getFillable())) {
//                unset($this->{$attrKey});
//            }
//        }
//    }

    public function load($data = [])
    {
        $this->oldObject = clone $this;

//        DD::dd($this->oldObject);
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
            // old
//            if (property_exists($this, $key)) {
//                $this->{$key} = $value;
//            }
        }
    }

    public function loadToAttributes($data = [])
    {
        $this->oldObject = clone $this;

//        DD::dd($this->oldObject);
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    protected function booted()
    {
        // it is created and ready for any other secondary tasks.
    }

    public function getFillable(): array
    {
        return $this->fillable;
    }

    abstract protected function scenarios(): array;

    protected function getActiveScenario(): array
    {
        if (is_null($this->scenario)) {
            if (empty($this->getFillable())) {
                throw new \InvalidArgumentException("Please set a scenario");
            }
            return $this->getFillable();
        }

        if ($this->scenario && empty($this->scenarios())) {
            throw new \InvalidArgumentException("Please make sure you have the right scenarios selected");
        }

        if (isset($this->scenarios()[$this->scenario])) {
            return $this->scenarios()[$this->scenario];
        }
        throw new \InvalidArgumentException("Scenario not found, please set a correct one or add a new");
    }

    public function validate($attributes = null, $clearErrors = true): bool
    {
        if ($clearErrors) {
            $this->clearErrors();
        }
//        $this->beforeValidation();
        $scenarioAttributes = $attributes ?? $this->getActiveScenario();
        $scenarioAttributes = $scenarioAttributes ?? $this->getFillable();
        $scenarioAttributes = is_string($scenarioAttributes) ? [$scenarioAttributes] : $scenarioAttributes;

        $rules = $this->rules();
//        DD::dd(app('validator')->validate(['sfadf'], ['rules']));
//        DD::dd($this->errors);
        foreach ($rules as $item => $rule) {
//            DD::dl($item);
//            DD::dl($rule);
            list($ruleAttributes, $validator, $params) = $this->getValidatorAndAttributes($item, $scenarioAttributes, $rule);
            // TODO compare $ruleAttributes with $scenarioAttributes
//            DD::dd($ruleAttributes);
            validate($this, $ruleAttributes, $validator, $params);

//            list($ruleAttributes, $ruleValidators) = $this->getAttributesAndRules($item, $rule, $scenarioAttributes);
//            $this->handleValidationRules($ruleAttributes, $ruleValidators);
//            DD::dd($ruleAttributes);
        }

//        DD::dd('out of rules loop');
//        DD::dd($this->errors);
        if ($this->hasErrors()) {
            $this->notifyValidationErrors();
        }

//        $this->afterValidation();
        if ($this->checkIfHasChanges) {
            return !$this->hasErrors() && $this->isDirty();
        }
        return !$this->hasErrors();
    }

    protected function private($params)
    {
        DD::dl($params);
        echo 'private';
        exit;
    }

    protected function addErrorForRule(string $attr, string $ruleName, $params = [])
    {
        // get the message for the specific RuleInterface
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
//            return array_search(Rules::REQUIRED, $rules[$fieldName]) !== false;
//        }
//
//        foreach ($rules as $rule) {
//            if (in_array($fieldName, $rule[0]) && in_array('')) {
//
//            }
//        }
//    }

    public function addError(string $attr, string $message, string $validator = '')
    {
        if ($message) {
//            $this->errors[$attr][] = $message;
            $this->errors[$attr] = [];
            $this->errors[$attr][$validator] = $message;
        }
    }

    public function errorMessages(): array
    {
        return [
            Rules::REQUIRED => 'This field is required',
            Rules::EMAIL => 'This field must be a valid email address',
            Rules::MIN_STR => 'This field must contain at least {min} chars',
            Rules::MAX_STR => 'This field must contain at most {max} chars',
            Rules::MATCH => 'This field  must be same as {match}',
            Rules::UNIQUE => 'Record with this {field} already exists',
            Rules::MSG_UNIQUE_TOO_SHORT => 'The {field} must have at least {n} chars',
            Rules::ARRAY_FROM_STR => 'The {field} must have provide a property to fill',
            Rules::IN_ARRAY => 'The {field} must be in {value_list}',
        ];
    }

    public function hasError($fieldName): bool
    {
        if ($this->hasErrors()) {
            return isset($this->errors[$fieldName]);
        }
        return false;
    }

    public function hasErrors(): bool
    {
        return !UtilsHelper::isEmpty($this->errors);
    }

    public function getFirstError($fieldName): string
    {
        if ($this->hasError($fieldName)) {
            $ruleName = array_key_first($this->errors[$fieldName]);
            return $this->errors[$fieldName][$ruleName];
        }
        return '';
//        if ($this->hasError($fieldName)) {
//            return $this->errors[$fieldName][0];
//        }
//        return '';
    }

    public function labels(): array
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

//    public static function delete(int $id)
//    {
//        Query::getInst()->delete(static::tableName(), $id);
//    }

    public static function deleteWhereIn($field, array $arr): int
    {
        return Query::getInst()->deleteWhereIn(static::tableName(), $field, $arr);
    }

//    public static function update($data, $where): int
//    {
//        return Query::getInst()->update(static::tableName(), $data, $where);
//    }

    /**
     * @param $attributes
     */
    protected function checkIncomingAttributes($attributes): void
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

    protected function isDirty(): bool
    {
        if (isset($this->oldObject)) {
            foreach ($this->oldObject as $attrName => $attrValue) {
                if (in_array($attrName, $this->getFillable()) && $this->oldObject->{$attrName} != $this->{$attrName}) {
                    $this->addDirty($attrName);
                }
            }
        }

        if (empty($this->dirty)) {
            Application::app()->session->setFlash(Message::WARNING, $this->validationTitle, 'No fields were changed');
        }
//        DD::dd($this->dirty);
        return count($this->dirty) > 0;
    }

    protected function clearErrors($attribute = null)
    {
        if (is_null($attribute)) {
            $this->errors = [];
        } else {
            unset($this->errors[$attribute]);
        }
    }

    protected function notifyValidationErrors(): void
    {
        $attribNames = array_keys($this->errors);
        $fields = array_map(fn($attr) => sprintf("<li>%s</li>", $this->getLabel($attr)), $attribNames);
        $desc = sprintf("%s:<br /><ul>", $this->validationGeneralDescription) . implode("\n", $fields);
        $desc .= "</ul>";
        Application::$app->session->setFlash(
            Message::DANGER,
            $this->validationTitle,
            $desc,
            Message::ADMIN_VISIBLE
        );
    }

    /**
     * @param $item
     * @param $scenarioAttributes
     * @param $rule
     * @return array
     */
    protected function getValidatorAndAttributes($item, $scenarioAttributes, $rule): array
    {
        $ruleValidator = '';
        $ruleAttributes = $params = [];
        $singleAttribute = is_string($item) && in_array($item, $scenarioAttributes, true);
        if ($singleAttribute) {
            $ruleAttributes[] = $item;
            if ($singleRule = is_string($rule)) {
                $ruleValidator = $rule;
            } else {
                $ruleValidator = isset($rule[0]) ? array_shift($rule) : null;
                $params = $rule;
            }
        } elseif (is_array($rule) && isset($rule[0], $rule[1])) {
            $ruleAttribute = array_shift($rule);
            if (is_array($rule[0])) {
                $ruleValidator = array_shift($rule[0]);
                $params = $rule[0];
            } else {
                $ruleValidator = array_shift($rule);
            }
            if (is_array($ruleAttribute) && !empty(array_intersect($ruleAttribute, $scenarioAttributes))) {
                $ruleAttributes = $ruleAttribute;
            } elseif (in_array($ruleAttribute, $scenarioAttributes, true)) {
                $ruleAttributes[] = $ruleAttribute;
            }
            $ruleValidator = is_array($ruleValidator) ? $ruleValidator[0] : $ruleValidator;
            $params = empty($params) && ArrayHelper::isAssoc($rule) ? $rule : $params;
        }
        if (empty($ruleValidator)) {
            throw new \InvalidArgumentException('$ruleValidator must be set 
            [NOTE: check if you have field in scenario or in fiilable]');
        }
        if (empty($ruleAttributes)) {
            throw new \InvalidArgumentException('$ruleAttributes must be set');
        }
        return array($ruleAttributes, $ruleValidator, $params);
    }
}
