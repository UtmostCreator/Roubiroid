<?php

namespace Framework\validation;

use Framework\Model;
use Modules\DD;

class ValidationManager
{
    protected array $rules = [];

//    // TODO related to function "private function private"
//    public function test($call)
//    {
//        $call(['error1', 'error2']);
//    }


    protected static ?ValidationManager $inst = null;

    public static function getInst(): self
    {
        if (is_null(self::$inst)) {
            self::$inst = new static();
        }

        return self::$inst;
    }

    public function addRule(string $alias, BaseRule $rule): self
    {
        $this->rules[$alias] = $rule;
        return $this;
    }

    // TODO add validate array | validateModel to allow array validation

    /**
     * @param $validator string|callable|BaseRule
     */
    public function validate(Model $model, array $attributes, $validator, array $params = []): array
    {
        /* @var $processor BaseRule|callable could be either a callable method form model or RuleBase object */
        $processor = null;
        $errors = [];
        if (isset($this->rules[$validator])) {
            $processor = $this->rules[$validator];
        } elseif (class_exists($validator)) {
            $processor = new $validator();
            if (!$processor instanceof RuleInterface) {
                throw new \InvalidArgumentException("{$validator} class is not instance of " . RuleInterface::class);
            }
        }
//        is_subclass_of(RuleInterface::class, $validator, true)
        foreach ($attributes as $attribute) {
            if (isset($params['skipOnEmpty']) && in_array($params['skipOnEmpty'], [true, "true", "yes"], true)) {
                continue;
            }
            if (is_null($processor)) {
                if (method_exists($model, $validator)) {
//                $model->$validator($model, $attribute, $params);
                    $model->$validator($attribute, $params);
                    continue;
                }
                throw new \InvalidArgumentException("\$processor is not defined 
                [make sure your validator exists or model`s method is in the correct Model]
                Validator Name: '$validator'
                Model: " . get_class($model));
            }
            if (!$processor->validate($model, $attribute, $params)) {
                if ($model->hasErrors() && $model::SINGLE_ERROR_MESSAGE) {
                    continue;
//                        continue; // probably must be uncommented
                }
                $errorMsg = $processor->getMessage($model, $attribute, $params);
                $errors[$attribute] = [];
                $errors[$attribute][$validator] = $errorMsg;

                $model->addError($attribute, $errorMsg, $validator);
            }
        }
        return $errors;
        DD::dd('Validation Manager');
        DD::dd($this);
    }
}
