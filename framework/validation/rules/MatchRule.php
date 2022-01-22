<?php

namespace Framework\validation\rules;

use Framework\Model;
use Framework\validation\BaseRule;
use Modules\DD;

class MatchRule extends BaseRule
{

    public function validate(Model $model, string $field, array $params): bool
    {
        parent::validateParams($params);
        $comparedField = $params['value'];
        $comparedValue = $model->$comparedField ?? $params['value'];
        return $model->$field === $comparedValue;
    }

    public function getMessage(Model $model, string $field, array $params): string
    {
        $comparedField = $params['value'];
        return sprintf(
            "%s do not match with %s",
            parent::fieldNameToUpperCase($params['value']),
            parent::fieldNameToUpperCase($field)
        );
    }
}
