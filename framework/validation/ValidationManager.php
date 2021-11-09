<?php

namespace Framework\validation;

class Manager
{
    protected array $rules = [];

    public function test($call)
    {
        $call(['error1', 'error2']);
    }

    public function addRule(string $alias, Rule $rule): self
    {
        $this->rules[$alias] = $rule;
        return $this;
    }

    public function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $rulesForFields) {
            foreach ($rulesForFields as $rule) {
                $name = $rule;
                $params = [];

                if (str_contains($rule, ":")) {
                    [$name, $params] = explode(':', $rule);
                    $params = explode(',', $params);
                }

                $processor = $this->rules[$name];

                if (!$processor->validate($data, $field, $params)) {
                    if (!isset($errors[$field])) {
                        $errors[$field] = [];
//                        continue; // probably must be uncommented
                    }

                    array_push($errors[$field], $processor->getMessage($data, $field, $params));
                }
            }
        }

        if (count($errors)) {
            $exception = new ValidationException();
            $exception->setErrors($errors);
            throw $exception;
        }

        return array_intersect_key($data, $rules);
    }

}
