<?php

namespace Framework\validation;

interface Rule
{
    public function validate(array $data, $field, array $params): bool;
    public function getMessage(array $data, $field, array $params): string;
}
