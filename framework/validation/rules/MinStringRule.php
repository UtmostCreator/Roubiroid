<?php

namespace Framework\validation\rules;

use Framework\Model;
use Framework\Rules;
use Framework\validation\BaseRule;
use Modules\DD;

class MinStringRule extends BaseRule
{

    public function validate(Model $model, string $field, array $params): bool
    {
        parent::validateParams($params);
        $length = $params[0] ?? $params[Rules::MIN_STR];
        return strlen($model->$field) >= $length;
    }

    public function getMessage(Model $model, string $field, array $params): string
    {
        $length = $params[0] ?? $params[Rules::MIN_STR];
        $field = $this->fieldNameToUpperCase($field);
        return $params['message'] ?? "{$field} should be at least {$length} characters";
    }
}
