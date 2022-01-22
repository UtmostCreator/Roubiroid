<?php

namespace Framework\validation\rules;

use Framework\Model;
use Framework\validation\BaseRule;

class InArrayRule extends BaseRule
{

    public function validate(Model $model, string $field, array $params): bool
    {
        parent::validateParams($params);
        if (!is_array($params['arr'])) {
            return false;
        }
        return in_array($model->$field, $params['arr']);
    }

    public function getMessage(Model $model, string $field, array $params): string
    {
        return sprintf(
            "%s must contain any of the following values: [%s]",
            parent::fieldNameToUpperCase($field),
            '"' . implode('", "', $params['arr']) . '"'
        );
    }
}
