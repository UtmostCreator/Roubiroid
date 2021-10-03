<?php

namespace Framework\validation\rules;

use Framework\validation\Rule;

class EmailRule implements Rule
{

    public function validate(array $data, $field, array $params): bool
    {
        if (empty($data[$field])) {
            return true;
        }

        return filter_var($data[$field], FILTER_VALIDATE_EMAIL);
    }

    public function getMessage(array $data, $field, array $params): string
    {
        return "{$field} should be an email";
    }
}
