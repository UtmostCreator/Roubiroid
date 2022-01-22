<?php

namespace Framework\validation\rules;

use Framework\Model;
use Framework\validation\BaseRule;

class MinNumberRule extends BaseRule
{

    public function validate(Model $model, string $field, array $params): bool
    {
        parent::validateParams($params);

        $num = $params['min'];
        return $model->$field >= $num;
    }

    public function getMessage(Model $model, string $field, array $params): string
    {
        $num = $params['min'];
        return "The minimum is number is {$num}";
    }
}
