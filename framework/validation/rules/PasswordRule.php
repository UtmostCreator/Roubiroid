<?php

namespace Framework\validation\rules;

use Framework\Model;
use Framework\validation\BaseRule;

// 'min' => 6, 'max' => 48, pattern => "/regexp/" - required symbols
class PasswordRule extends BaseRule
{

    public function validate(Model $model, string $field, array $params): bool
    {
        $minProcessor = new MinStringRule();
        if ($minProcessor->validate($model, $field, $params)) {
            return false;
        }
        $maxProcessor = new MaxStringRule();
        if (!$maxProcessor->validate($model, $field, $params)) {
            return false;
        }

        return true;
    }

    public function getMessage(Model $model, string $field, array $params): string
    {
        return "Password Rule error";
    }
}
