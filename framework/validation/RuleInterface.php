<?php

namespace Framework\validation;

use Framework\Model;

interface RuleInterface
{
    public function validate(Model $model, string $field, array $params): bool;
    public function getMessage(Model $model, string $field, array $params): string;
}
