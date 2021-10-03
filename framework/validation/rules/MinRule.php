<?php

namespace Framework\validation\rules;

class MinRule implements \Framework\validation\Rule
{

    public function validate(array $data, $field, array $params): bool
    {
        if (empty($data[$field])) {
            return true;
        }

        if ($params[0]) {
            throw new \InvalidArgumentException('specify a min length');
        }

        $length = (int)$params[0];
        return strlen($data[$field]) >= $length;
    }

    public function getMessage(array $data, $field, array $params): string
    {
        $length = (int)$params[0];
        return "{$field} should be at least {$length} characters";
    }
}
