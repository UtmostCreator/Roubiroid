<?php

namespace Framework\validation\rules;

class RequiredRule implements \Framework\validation\Rule
{

    public function validate(array $data, $field, array $params): bool
    {
        return !empty($data[$field]);
    }

    public function getMessage(array $data, $field, array $params): string
    {
        return "{$field} is required";
    }
}
