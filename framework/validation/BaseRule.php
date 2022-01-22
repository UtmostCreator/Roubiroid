<?php

namespace Framework\validation;

use Framework\Model;
use Modules\DD;

abstract class BaseRule implements RuleInterface
{
    public function validate(Model $model, string $field, array $params): bool
    {
        return false;
    }

    public function getMessage(Model $model, string $field, array $params): string
    {
        if (isset($params['message'])) {
            if (strpos($params['message'], '{field}') !== false) {
                return str_replace('{field}', ucfirst($field), $params['message']);
            }

            return $params['message'];
        }
        return false;
    }

    public function validateParams(array $params)
    {
        if (empty($params)) {
            throw new \InvalidArgumentException(sprintf('%s has empty $params array', get_called_class()));
        }
    }

    protected function fieldNameToUpperCase($field): string
    {
        preg_match_all('/[A-Z]/', $field, $matches, PREG_OFFSET_CAPTURE);
        if (count($matches) > 0) {
            $upperCasePos = array_map(function ($el) {
                return isset($el[0]) && isset($el[0][0]) ? $el[0][0] : '';
            }, $matches);
            $upperCasePos = array_filter($upperCasePos);
            $field = str_replace($upperCasePos, array_map(fn($el) => " {$el}", $upperCasePos), $field);
        }
        return ucfirst($field);
    }
}
