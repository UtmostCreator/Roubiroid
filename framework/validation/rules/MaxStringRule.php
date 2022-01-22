<?php

namespace Framework\validation\rules;

use Framework\Model;
use Framework\validation\BaseRule;

class MaxStringRule extends BaseRule
{

    public function validate(Model $model, string $field, array $params): bool
    {
        parent::validateParams($params);

        $length = (int)$params[0];
        return strlen($model->$field) <= $length;
    }

    public function getMessage(Model $model, string $field, array $params): string
    {
        $length = (int)$params[0];
        $field = $this->fieldNameToUpperCase($field);
        return "{$field} should have at most {$length} characters";
    }
}
