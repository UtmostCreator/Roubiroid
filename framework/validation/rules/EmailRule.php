<?php

namespace Framework\validation\rules;

use Framework\Model;
use Framework\validation\BaseRule;

class EmailRule extends BaseRule
{

    public function validate(Model $model, string $field, array $params): bool
    {
        return filter_var($model->$field, FILTER_VALIDATE_EMAIL);
    }

    public function getMessage(Model $model, string $field, array $params): string
    {
        return sprintf("%s should be an email", parent::fieldNameToUpperCase($field));
    }
}
