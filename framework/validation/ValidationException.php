<?php

namespace Framework\validation;

class ValidationException extends \InvalidArgumentException
{
    protected array $errors = [];

    public function setErrors(array $errors): self
    {
        $this->errors = $errors;
        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
