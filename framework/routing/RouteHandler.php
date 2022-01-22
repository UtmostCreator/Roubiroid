<?php

namespace Framework\routing;

use InvalidArgumentException;

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
        if (empty($callback)) {
            throw new InvalidArgumentException("Callback is wronly formed");
        }
        $this->callback = $callback;
        $this->className = $this->callback[0];
        $this->methodName = $this->callback[1];
        if (!class_exists($this->className)) {
            throw new InvalidArgumentException("Class '{$this->className}' does not exist!");
        }
        if (!method_exists($this->className, $this->methodName)) {
            throw new InvalidArgumentException("Class: {$this->className} do not define '{$this->methodName}' method");
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