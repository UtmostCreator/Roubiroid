<?php

namespace Framework\routing;

class RouteHandler
{
    protected ?string $className;
    protected ?string $methodName;
    protected ?array $callback;

    /**
     * @param array $callback
     * @throws \Exception
     */
    public function __construct(array $callback = [])
    {
        $this->callback = $callback;
        if (!empty($this->callback)) {
            $this->className = $this->callback[0];
            $this->methodName = $this->callback[1];
            if (!class_exists($this->className)) {
                throw new \Exception("Class '{$this->className}' does not exist!");
            }
            if (!method_exists($this->className, $this->methodName)) {
                throw new \Exception("Method called '{$this->methodName}' does not exist in class '{$this->className}'");
            }
        }
    }

    public function __get($name)
    {
        if (property_exists($this, $name) || method_exists($this, 'get' . ucfirst($name))) {
            return $this->{$name};
        }

        return null;
    }

}