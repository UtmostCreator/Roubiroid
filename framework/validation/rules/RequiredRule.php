<?php

namespace Framework\validation\rules;

use Framework\helpers\UtilsHelper;
use Framework\Model;
use Framework\validation\BaseRule;
use Modules\DD;

class RequiredRule extends BaseRule
{

    public function validate(Model $model, string $field, array $params): bool
    {
        return !UtilsHelper::isEmpty($model->$field);
    }

    public function getMessage(Model $model, string $field, array $params): string
    {
        if ($message = parent::getMessage($model, $field, $params)) {
            return $message;
        }

        $field = $this->fieldNameToUpperCase($field);
        return sprintf("%s is required", $field);
    }
}
