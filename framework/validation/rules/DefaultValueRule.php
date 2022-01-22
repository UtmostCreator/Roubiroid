<?php

namespace Framework\validation\rules;

use Framework\Model;
use Framework\validation\BaseRule;

class DefaultValueRule extends BaseRule
{

    public function validate(Model $model, string $field, array $params): bool
    {
        parent::validateParams($params);
        return $model->$field = $params[0];
    }

    public function getMessage(Model $model, string $field, array $params): string
    {
        return "Something went wrong, default value is not set";
    }
}
